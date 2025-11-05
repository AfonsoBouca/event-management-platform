SET search_path TO lbaw25124;

-- Drop tables 
DROP TABLE IF EXISTS eventNotification, favouriteEvent, eventParticipation,
userReport, commentReport, eventReport, report, pollOption, poll, invitation,
notification, comment, event, upload, joinRequest, appAdmin, tag, category, vote, "user" CASCADE;

-- Drop enums
DROP TYPE IF EXISTS RoleType, EventStatus, NotificationType, ReportType, R_J_Status CASCADE;

-- Enum Types
CREATE TYPE RoleType AS ENUM ('Attendee', 'Organizer');
CREATE TYPE EventStatus AS ENUM ('Upcoming', 'Ongoing', 'Completed', 'Cancelled');
CREATE TYPE NotificationType AS ENUM ('EventUpdate', 'NewComment','PollCreated','PollClosed','Invitation', 'JoinRequest', 'ReportStatus');
CREATE TYPE ReportType AS ENUM ('FraudulentEvent', 'ImproperLanguage', 'Other');
CREATE TYPE R_J_Status AS ENUM ('Pending', 'Accepted', 'Rejected');

-- Tables

CREATE TABLE "user" (
    userId SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    passwordHash VARCHAR(255) NOT NULL,
    profileImage VARCHAR(255),
    registrationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    isAdmin BOOLEAN DEFAULT FALSE
);

CREATE TABLE event (
    eventId SERIAL PRIMARY KEY,
    eventName VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    startDate TIMESTAMP NOT NULL,
    endDate TIMESTAMP NOT NULL,
    capacity INT CHECK (capacity >= 0),
    price INT DEFAULT 0 CHECK (price >= 0),
    isPublic BOOLEAN DEFAULT FALSE,
    location VARCHAR(255) NOT NULL,
    createdBy INT REFERENCES "user"(userId) ON DELETE CASCADE,
    eventStatus EventStatus NOT NULL DEFAULT 'Upcoming'
    CHECK (endDate > startDate)
);

CREATE TABLE comment (
    commentId SERIAL PRIMARY KEY,
    content TEXT NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    authorId INT REFERENCES "user"(userId) ON DELETE CASCADE,
    eventId INT REFERENCES event(eventId) ON DELETE CASCADE,
    parentId INT REFERENCES comment(commentId) ON DELETE CASCADE
);

CREATE TABLE notification (
    notificationId SERIAL PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    isRead BOOLEAN DEFAULT FALSE,
    notificationType NotificationType NOT NULL
);

CREATE TABLE appAdmin (
    adminId SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    passwordHash VARCHAR(255) NOT NULL
);

CREATE TABLE invitation (
    invitationId SERIAL PRIMARY KEY,
    eventId INT REFERENCES event(eventId) ON DELETE CASCADE,
    userId INT REFERENCES "user"(userId) ON DELETE CASCADE,
    confirmation BOOLEAN DEFAULT FALSE
);

CREATE TABLE poll (
    pollId SERIAL PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    eventId INT REFERENCES event(eventId) ON DELETE CASCADE
);

CREATE TABLE pollOption (
    pollOptionId SERIAL PRIMARY KEY,
    pollId INT REFERENCES poll(pollId) ON DELETE CASCADE,
    optionText VARCHAR(255) NOT NULL
);

CREATE TABLE report (
    reportId SERIAL PRIMARY KEY,
    userId INT REFERENCES "user"(userId) ON DELETE CASCADE,
    description TEXT NOT NULL,
    reportTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reportType ReportType NOT NULL,
    status R_J_Status NOT NULL 
);

CREATE TABLE eventReport (
    reportId INT REFERENCES report(reportId) ON DELETE CASCADE,
    eventId INT REFERENCES event(eventId) ON DELETE CASCADE,
    PRIMARY KEY (reportId, eventId)
);

CREATE TABLE commentReport (
    reportId INT REFERENCES report(reportId) ON DELETE CASCADE,
    commentId INT REFERENCES comment(commentId) ON DELETE CASCADE,
    PRIMARY KEY (reportId, commentId)
);

CREATE TABLE userReport (
    reportId INT REFERENCES report(reportId) ON DELETE CASCADE,
    userId INT REFERENCES "user"(userId) ON DELETE CASCADE,
    PRIMARY KEY (reportId, userId)
);

CREATE TABLE eventParticipation (
    eventId INT REFERENCES event(eventId) ON DELETE CASCADE,
    userId INT REFERENCES "user"(userId) ON DELETE CASCADE,
    role RoleType NOT NULL,
    PRIMARY KEY (eventId, userId)
);

CREATE TABLE favouriteEvent (
    favouriteId SERIAL PRIMARY KEY,
    eventId INT REFERENCES event(eventId) ON DELETE CASCADE,
    userId INT REFERENCES "user"(userId) ON DELETE CASCADE,
    addedDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE eventNotification (
    notificationId INT REFERENCES notification(notificationId) ON DELETE CASCADE,
    eventId INT REFERENCES event(eventId) ON DELETE CASCADE,
    PRIMARY KEY (notificationId, eventId)
);

CREATE TABLE category (
    categoryId SERIAL PRIMARY KEY,
    categoryName VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE tag (
    tagId SERIAL PRIMARY KEY,
    tagName VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE vote (
    userId INT REFERENCES "user"(userId) ON DELETE CASCADE,
    commentId INT REFERENCES comment(commentId) ON DELETE CASCADE,
    PRIMARY KEY (userId, commentId)
);

CREATE TABLE joinRequest (
    joinId SERIAL PRIMARY KEY,
    j_status R_J_Status NOT NULL DEFAULT 'Pending',
    userId INT REFERENCES "user"(userId) ON DELETE CASCADE,
    eventId INT REFERENCES event(eventId) ON DELETE CASCADE
);

CREATE TABLE upload (
    uploadId SERIAL PRIMARY KEY,
    filePath VARCHAR(255) NOT NULL
);

-- Indexes
CREATE INDEX idx_user_email ON "user" USING btree(email);
CREATE INDEX idx_event_createdBy ON event USING btree(createdBy);
CREATE INDEX idx_comment_eventId ON comment USING btree(eventId);
CREATE INDEX idx_eventParticipation_eventId ON eventParticipation USING btree(eventId);

-- FTS (Full Text Search)
ALTER TABLE event ADD COLUMN event_tsv tsvector;
CREATE INDEX event_search ON event USING GIN(event_tsv);

-- Optional trigger for automatic FTS update
CREATE OR REPLACE FUNCTION event_tsv_trigger() RETURNS trigger AS $$
BEGIN
  NEW.event_tsv :=
    to_tsvector('english', COALESCE(NEW.eventName, '') || ' ' || COALESCE(NEW.description, ''));
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tsv_update BEFORE INSERT OR UPDATE
  ON event FOR EACH ROW EXECUTE FUNCTION event_tsv_trigger();

--TRIGGER 1
CREATE OR REPLACE FUNCTION add_attendee_from_invitation() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NEW.confirmation = TRUE AND (OLD.confirmation IS DISTINCT FROM TRUE) THEN
        INSERT INTO eventParticipation(eventId, userId, role)
        VALUES (NEW.eventId, NEW.userId, 'Attendee')
        ON CONFLICT (eventId, userId) DO NOTHING;
    END IF;
    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER invitation_accept_trigger
AFTER UPDATE ON invitation
FOR EACH ROW
EXECUTE FUNCTION add_attendee_from_invitation(); 

--TRIGGER 2
CREATE OR REPLACE FUNCTION add_attendee_from_join_request() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NEW.j_status = 'Accepted' AND (OLD.j_status IS DISTINCT FROM 'Accepted') THEN
        INSERT INTO eventParticipation(eventId, userId, role)
        VALUES (NEW.eventId, NEW.userId, 'Attendee')
        ON CONFLICT (eventId, userId) DO NOTHING;
    END IF;
    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER join_request_accept_trigger
AFTER UPDATE ON joinRequest
FOR EACH ROW
EXECUTE FUNCTION add_attendee_from_join_request();

--TRIGGER 3
 CREATE OR REPLACE FUNCTION notify_attendees_on_event_update()
 RETURNS TRIGGER AS
 $BODY$
 BEGIN
   IF (OLD.eventName IS DISTINCT FROM NEW.eventName)
      OR (OLD.description IS DISTINCT FROM NEW.description)
      OR (OLD.startDate IS DISTINCT FROM NEW.startDate)
      OR (OLD.endDate IS DISTINCT FROM NEW.endDate)
      OR (OLD.location IS DISTINCT FROM NEW.location)
   THEN  
      INSERT INTO notification (title, message, notificationType)
      SELECT
      'Event Updated',
           'The event ""' || NEW.eventName || '" has been updated. Please check the details.',
           'EventUpdate'
      FROM eventParticipation
      WHERE eventParticipation.eventId = NEW.eventId
      AND role = 'Attendee';
      
      INSERT INTO eventNotification (notificationId, eventId)
      SELECT n.notificationId, NEW.eventId
      FROM notification n
      WHERE n.notificationType = 'EventUpdate'
      AND n.title = 'Event Update'
      ORDER BY n.notificationId DESC
      LIMIT 1;    
            
   END IF;
   
   RETURN NEW;
END   
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER trg_notify_attendees_on_event_update
AFTER UPDATE ON event
FOR EACH ROW
EXECUTE FUNCTION notify_attendees_on_event_update();

--TRIGGER 4
CREATE OR REPLACE FUNCTION notify_organizer_on_new_comment()
RETURNS TRIGGER AS
$BODY$
DECLARE 
    organizer_id INT;
BEGIN

    SELECT createdBy INTO organizer_id
    FROM event
    WHERE eventId = NEW.eventId;
    
    IF organizer_id IS NOT NULL AND organizer_id <> NEW.authorId THEN 
    INSERT INTO notification (title, message, notificationType)
    VALUES (
        'New Comment',
    'A new comment was added to your event.',
    'NewComment'
    );   
    
    END IF;
  
  RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER trg_notify_organizer_on_new_comment
AFTER INSERT ON comment
FOR EACH ROW
EXECUTE FUNCTION notify_organizer_on_new_comment();

--TRIGGER 5
CREATE OR REPLACE FUNCTION notify_organizer_on_join_request()
RETURNS TRIGGER AS
$BODY$
DECLARE 
     organizer_id INT;
     organizer_exists BOOLEAN;
BEGIN
     SELECT createdBy INTO organizer_id
     FROM event
     WHERE eventId = NEW.eventId;
     
     IF organizer_id IS NOT NULL AND organizer_id <> NEW.userId THEN
     
     INSERT INTO notification(title, message, notificationType)
     VALUES (
        'Join Request' ,
     'A user has requested to join your event',
     'JoinRequest'
     );
     
     END IF;
    
     RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER trg_notify_organizer_on_join_request
AFTER INSERT ON joinRequest
FOR EACH ROW
EXECUTE FUNCTION notify_organizer_on_join_request();

--TRIGGER 6
CREATE OR REPLACE FUNCTION notify_attendees_about_poll()
RETURNS TRIGGER AS
$BODY$
DECLARE 
    attendee RECORD;
BEGIN
 
    FOR attendee IN
        SELECT userId
        FROM eventParticipation
        WHERE eventId = NEW.eventId
          AND role = 'Attendee'
    LOOP      
      INSERT INTO notification(title, message, notificationType)
      VALUES (
        'New Poll',
      'A new poll has been created for an event you are attending.',
      'PollCreated'
      );
      
    END LOOP;
    
    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER trg_notify_attendees_about_poll
AFTER INSERT ON poll
FOR EACH ROW
EXECUTE FUNCTION notify_attendees_about_poll();

--TRIGGER 7
CREATE OR REPLACE FUNCTION notify_admin_on_report()
RETURNS TRIGGER AS
$BODY$
DECLARE
    admin RECORD;
BEGIN
   
   FOR admin IN
       SELECT adminId FROM appAdmin
   LOOP
       INSERT INTO notification (title, message, notificationType)
       VALUES (
        'New Report',
       'A new user report has been submitted and requires review.',
       'ReportStatus'
       );
   END LOOP;    
   
   RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER trg_notify_admin_on_report
AFTER INSERT ON report
FOR EACH ROW
EXECUTE FUNCTION notify_admin_on_report();

--TRIGGER 8
CREATE OR REPLACE FUNCTION notify_organizer_on_capacity_reach()
RETURNS TRIGGER AS
$BODY$
DECLARE
   organizer_id INT;
   attendee_count INT;
   event_capacity INT;
BEGIN
    SELECT COUNT(*) INTO attendee_count
    FROM eventParticipation
    WHERE eventId = NEW.eventId;
    
    
    SELECT capacity, createdBy INTO event_capacity, organizer_id
    FROM event
    WHERE
    eventId = NEW.eventId;
    
    IF attendee_count = event_capacity THEN
        INSERT INTO notification (title, message, notificationType)
        VALUES (
            'Capacity Reached',
        'Your event has reached its maximum capacity of ' || event_capacity || 'attendees.',
        'EventUpdate'
        );
    END IF;
    
    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER trg_notify_organizer_on_capacity_reach
AFTER INSERT ON eventParticipation
FOR EACH ROW
EXECUTE FUNCTION notify_organizer_on_capacity_reach();

--TRIGGER 9
CREATE OR REPLACE FUNCTION add_default_poll_options()
RETURNS TRIGGER AS
$BODY$
BEGIN
    INSERT INTO pollOption (pollId, optionText)
    VALUES
       (NEW.pollId, 'Yes'),
       (NEW.pollId, 'No');    
    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER poll_default_options_trigger
AFTER INSERT ON poll
FOR EACH ROW
EXECUTE FUNCTION add_default_poll_options();  

--TRIGGER 10
CREATE OR REPLACE FUNCTION add_organizer_on_eventParticipation()
RETURNS TRIGGER AS
$BODY$
BEGIN
   INSERT INTO eventParticipation (eventId, userId, role)
   VALUES (NEW.eventId, NEW.createdBy, 'Organizer')
   ON CONFLICT (eventID, userID) DO NOTHING;
   RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER trg_add_organizer_on_eventParticipation
AFTER INSERT ON event
FOR EACH ROW
EXECUTE FUNCTION add_organizer_on_eventParticipation();