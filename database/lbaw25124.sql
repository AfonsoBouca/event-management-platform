DROP SCHEMA IF EXISTS lbaw25124 CASCADE;
CREATE SCHEMA IF NOT EXISTS lbaw25124;
SET search_path TO lbaw25124;

-- Drop tables 
DROP TABLE IF EXISTS eventCategory, eventTag, eventNotification, favouriteEvent, eventParticipation,
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
    isAdmin BOOLEAN DEFAULT FALSE,
    isDeleted BOOLEAN DEFAULT FALSE,
    remember_token VARCHAR(100)
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

CREATE TABLE eventPhoto (
    photoid SERIAL PRIMARY KEY,
    filepath VARCHAR(255) NOT NULL,
    eventId INT REFERENCES event(eventId) ON DELETE CASCADE,
    userId INT REFERENCES "user"(userId) ON DELETE CASCADE,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
    userId INT REFERENCES "user"(userId) ON DELETE CASCADE,
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

CREATE TABLE pollVote (
    pollOptionId INT REFERENCES pollOption(pollOptionId) ON DELETE CASCADE,
    userId INT REFERENCES "user"(userId) ON DELETE CASCADE,
    votedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (pollOptionId, userId)
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

CREATE TABLE eventCategory (
    eventId INT REFERENCES event(eventId) ON DELETE CASCADE,
    categoryId INT REFERENCES category(categoryId) ON DELETE CASCADE,
    PRIMARY KEY (eventId, categoryId)
);

CREATE TABLE eventTag (
    eventId INT REFERENCES event(eventId) ON DELETE CASCADE,
    tagId INT REFERENCES tag(tagId) ON DELETE CASCADE,
    PRIMARY KEY (eventId, tagId)
);

-- Indexes
CREATE INDEX idx_user_email ON "user" USING btree(email);
CREATE INDEX idx_event_createdBy ON event USING btree(createdBy);
CREATE INDEX idx_comment_eventId ON comment USING btree(eventId);
CREATE INDEX idx_eventParticipation_eventId ON eventParticipation USING btree(eventId);

-- FTS (Full Text Search)
ALTER TABLE event ADD COLUMN event_tsv tsvector;
CREATE INDEX event_search ON event USING GIN(event_tsv);

------------------------------------------------------------------------------------------------------------------------
-- Triggers
------------------------------------------------------------------------------------------------------------------------

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

------------------------------------------------------------------------------------------------------------------------
--TRIGGER 1 - 
------------------------------------------------------------------------------------------------------------------------
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
------------------------------------------------------------------------------------------------------------------------
--TRIGGER 2
------------------------------------------------------------------------------------------------------------------------
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
------------------------------------------------------------------------------------------------------------------------
--TRIGGER 3
------------------------------------------------------------------------------------------------------------------------
 CREATE OR REPLACE FUNCTION notify_attendees_on_event_update()
 RETURNS TRIGGER AS
 $BODY$
 BEGIN
   IF OLD.eventstatus IS DISTINCT FROM NEW.eventstatus
    OR OLD.eventname IS DISTINCT FROM NEW.eventname
    OR (OLD.description IS DISTINCT FROM NEW.description)
    OR (OLD.startdate IS DISTINCT FROM NEW.startdate)
    OR (OLD.enddate IS DISTINCT FROM NEW.enddate)
    OR (OLD.location IS DISTINCT FROM NEW.location)
   THEN  
      INSERT INTO notification (userid, title, message, notificationType)
      SELECT
              eventParticipation.userId,
      'Event Updated',
           'The event ""' || NEW.eventName || '" has been updated. Please check the details.',
           'EventUpdate'
      FROM eventParticipation
      WHERE eventParticipation.eventId = NEW.eventId
      AND role = 'Attendee';
      
   END IF;
   
   RETURN NEW;
END   
$BODY$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_notify_attendees_on_event_update ON event;

CREATE TRIGGER trg_notify_attendees_on_event_update
AFTER UPDATE ON event
FOR EACH ROW
EXECUTE FUNCTION notify_attendees_on_event_update();
------------------------------------------------------------------------------------------------------------------------
--TRIGGER 4
------------------------------------------------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS trg_notify_users_on_new_comment ON comment;
DROP FUNCTION IF EXISTS notify_users_on_new_comment();

CREATE OR REPLACE FUNCTION notify_users_on_new_comment()
RETURNS TRIGGER AS
$$
DECLARE
    organizer_id INT;
    event_name TEXT;
BEGIN
    SELECT e.createdBy, e.eventName
    INTO organizer_id, event_name
    FROM event e
    WHERE e.eventId = NEW.eventId;

    -- 1) Notificar Attendees (exclui autor)
    INSERT INTO notification (userId, title, message, notificationType)
    SELECT
        ep.userId,
        'New Comment',
        'A new comment was added to the event "' || event_name || '".',
        'NewComment'
    FROM eventParticipation ep
    WHERE ep.eventId = NEW.eventId
      AND ep.role = 'Attendee'
      AND ep.userId <> NEW.authorId;

    -- 2) Notificar Organizer (exclui autor)
    IF organizer_id IS NOT NULL AND organizer_id <> NEW.authorId THEN
        INSERT INTO notification (userId, title, message, notificationType)
        VALUES (
            organizer_id,
            'New Comment',
            'A new comment was added to your event "' || event_name || '".',
            'NewComment'
        );
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_notify_users_on_new_comment
AFTER INSERT ON comment
FOR EACH ROW
EXECUTE FUNCTION notify_users_on_new_comment();

------------------------------------------------------------------------------------------------------------------------
--TRIGGER 5
------------------------------------------------------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION notify_organizer_on_join_request()
RETURNS TRIGGER AS
$$
DECLARE
    organizer_id INT;
    event_name TEXT;
BEGIN
    SELECT createdBy, eventName
    INTO organizer_id, event_name
    FROM event
    WHERE eventId = NEW.eventId;

    IF organizer_id IS NOT NULL AND organizer_id <> NEW.userId THEN
        INSERT INTO notification (userId, title, message, notificationType)
        VALUES (
            organizer_id,
            'Join Request',
            'A user has requested to join your event "' || event_name || '".',
            'JoinRequest'
        );
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_notify_organizer_on_join_request ON joinRequest;

CREATE TRIGGER trg_notify_organizer_on_join_request
AFTER INSERT ON joinRequest
FOR EACH ROW
EXECUTE FUNCTION notify_organizer_on_join_request();
------------------------------------------------------------------------------------------------------------------------
--TRIGGER 6
------------------------------------------------------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION notify_attendees_about_poll()
RETURNS TRIGGER AS
$$
BEGIN
    INSERT INTO notification (userId, title, message, notificationType)
    SELECT
        ep.userId,
        'New Poll',
        'A new poll was created for the event "' || e.eventName || '".',
        'PollCreated'
    FROM eventparticipation ep
    JOIN event e ON e.eventId = NEW.eventId
    WHERE ep.eventId = NEW.eventId
      AND ep.role = 'Attendee';

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_notify_attendees_about_poll ON poll;

CREATE TRIGGER trg_notify_attendees_about_poll
AFTER INSERT ON poll
FOR EACH ROW
EXECUTE FUNCTION notify_attendees_about_poll();
------------------------------------------------------------------------------------------------------------------------
--TRIGGER 7
------------------------------------------------------------------------------------------------------------------------
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
------------------------------------------------------------------------------------------------------------------------
--TRIGGER 8
------------------------------------------------------------------------------------------------------------------------
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


--------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION notify_user_on_invitation()
RETURNS TRIGGER AS
$$
DECLARE
    event_name TEXT;
BEGIN
    SELECT eventName
    INTO event_name
    FROM event
    WHERE eventId = NEW.eventId;

    INSERT INTO notification (userId, title, message, notificationType)
    VALUES (
        NEW.userId,
        'Event Invitation',
        'You have been invited to the event "' || event_name || '".',
        'Invitation'
    );

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_notify_user_on_invitation ON invitation;

CREATE TRIGGER trg_notify_user_on_invitation
AFTER INSERT ON invitation
FOR EACH ROW
EXECUTE FUNCTION notify_user_on_invitation();
------------------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION notify_user_on_join_request_response()
RETURNS TRIGGER AS
$$
DECLARE
    event_name TEXT;
    response_text TEXT;
    notification_title TEXT;
BEGIN
    IF OLD.j_status IS DISTINCT FROM NEW.j_status THEN
        IF NEW.j_status IN ('Accepted', 'Rejected') THEN

            SELECT eventName
            INTO event_name
            FROM lbaw25124.event
            WHERE eventId = NEW.eventId;

            IF NEW.j_status = 'Accepted' THEN
                response_text := 'Your request to join the event "' || event_name || '" was accepted.';
                notification_title := 'Join Request Accepted';
            ELSE
                response_text := 'Your request to join the event "' || event_name || '" was rejected.';
                notification_title := 'Join Request Rejected';
            END IF;

            INSERT INTO lbaw25124.notification (userid, title, message, notificationType)
            VALUES (
                NEW.userid,
                notification_title,
                response_text,
                'JoinRequest'
            );
        END IF;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_notify_user_on_join_request_response
ON lbaw25124.joinRequest;

CREATE TRIGGER trg_notify_user_on_join_request_response
AFTER UPDATE ON lbaw25124.joinRequest
FOR EACH ROW
EXECUTE FUNCTION notify_user_on_join_request_response();


--------------------------------------------------------------

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

SET search_path TO lbaw25124;

-- Clear existing data (in correct order due to foreign keys)
DELETE FROM eventNotification;
DELETE FROM favouriteEvent;
DELETE FROM eventParticipation;
DELETE FROM userReport;
DELETE FROM commentReport;
DELETE FROM eventReport;
DELETE FROM report;
DELETE FROM vote;
DELETE FROM pollOption;
DELETE FROM poll;
DELETE FROM invitation;
DELETE FROM joinRequest;
DELETE FROM comment;
DELETE FROM notification;
DELETE FROM upload;
DELETE FROM event;
DELETE FROM tag;
DELETE FROM category;
DELETE FROM appAdmin;
DELETE FROM "user";

-- Reset sequences
ALTER SEQUENCE user_userid_seq RESTART WITH 1;
ALTER SEQUENCE event_eventid_seq RESTART WITH 1;
ALTER SEQUENCE comment_commentid_seq RESTART WITH 1;
ALTER SEQUENCE notification_notificationid_seq RESTART WITH 1;
ALTER SEQUENCE appadmin_adminid_seq RESTART WITH 1;
ALTER SEQUENCE invitation_invitationid_seq RESTART WITH 1;
ALTER SEQUENCE poll_pollid_seq RESTART WITH 1;
ALTER SEQUENCE polloption_polloptionid_seq RESTART WITH 1;
ALTER SEQUENCE report_reportid_seq RESTART WITH 1;
ALTER SEQUENCE favouriteevent_favouriteid_seq RESTART WITH 1;
ALTER SEQUENCE joinrequest_joinid_seq RESTART WITH 1;
ALTER SEQUENCE upload_uploadid_seq RESTART WITH 1;
ALTER SEQUENCE category_categoryid_seq RESTART WITH 1;
ALTER SEQUENCE tag_tagid_seq RESTART WITH 1;

-- Insert Users (passwords are hashed versions of 'password123')
INSERT INTO "user" (username, email, passwordHash, profileImage, registrationDate, isAdmin) VALUES
('john_doe', 'john.doe@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default.png', '2024-01-15 10:30:00', FALSE),
('jane_smith', 'jane.smith@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default.png', '2024-02-20 14:20:00', FALSE),
('mike_wilson', 'mike.wilson@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default.png', '2024-03-10 09:15:00', FALSE),
('sarah_jones', 'sarah.jones@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default.png', '2024-03-25 16:45:00', FALSE),
('david_brown', 'david.brown@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default.png', '2024-04-05 11:00:00', FALSE),
('emily_davis', 'emily.davis@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default.png', '2024-04-18 13:30:00', FALSE),
('alex_martin', 'alex.martin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default.png', '2024-05-02 10:00:00', FALSE),
('lisa_garcia', 'lisa.garcia@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default.png', '2024-05-20 15:20:00', FALSE),
('tom_rodriguez', 'tom.rodriguez@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default.png', '2024-06-01 12:10:00', FALSE),
('anna_lee', 'anna.lee@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default.png', '2024-06-15 14:50:00', FALSE),
('admin_user', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default.png', '2024-01-01 08:00:00', TRUE);

INSERT INTO appAdmin (username, email, passwordHash) VALUES
('superadmin', 'superadmin@eventplatform.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('admin_support', 'support@eventplatform.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert Categories
INSERT INTO category (categoryName) VALUES
('Technology'),
('Business'),
('Arts & Culture'),
('Sports'),
('Education'),
('Health & Wellness'),
('Music'),
('Food & Drink'),
('Networking'),
('Charity');

-- Insert Tags
INSERT INTO tag (tagName) VALUES
('Workshop'),
('Conference'),
('Seminar'),
('Meetup'),
('Festival'),
('Competition'),
('Exhibition'),
('Training'),
('Hackathon'),
('Webinar');

-- Insert Event (mix of Upcoming, Ongoing, Completed, Cancelled)
INSERT INTO event (eventName, description, startDate, endDate, capacity, price, isPublic, location, createdBy, eventStatus) VALUES
('Tech Conference 2025', 'Annual technology conference featuring latest innovations in AI and Machine Learning', '2025-12-01 09:00:00', '2025-12-03 18:00:00', 500, 150, TRUE, 'Silicon Valley Convention Center, CA', 1, 'Upcoming'),
('Startup Networking Mixer', 'Connect with fellow entrepreneurs and potential investors', '2025-11-15 18:00:00', '2025-11-15 22:00:00', 100, 25, TRUE, 'Downtown Business Hub, NYC', 2, 'Upcoming'),
('Art Gallery Opening', 'Exclusive opening night for contemporary art exhibition', '2025-11-10 19:00:00', '2025-11-10 23:00:00', 80, 0, TRUE, 'Metropolitan Art Gallery, Chicago', 3, 'Upcoming'),
('Marathon Training Workshop', 'Learn proper techniques and training plans for marathon runners', '2024-10-15 08:00:00', '2024-10-15 12:00:00', 50, 30, TRUE, 'City Sports Complex, Boston', 4, 'Completed'),
('Cooking Masterclass', 'Italian cuisine cooking class with renowned chef', '2025-11-08 15:00:00', '2025-11-08 19:00:00', 20, 80, FALSE, 'Culinary Institute, San Francisco', 5, 'Upcoming'),
('Music Festival 2024', 'Three-day outdoor music festival', '2024-08-20 12:00:00', '2024-08-22 23:00:00', 5000, 200, TRUE, 'Golden Gate Park, San Francisco', 1, 'Completed'),
('Charity Fundraiser Gala', 'Annual charity event supporting local education', '2025-12-15 19:00:00', '2025-12-15 23:00:00', 200, 100, TRUE, 'Grand Hotel Ballroom, LA', 6, 'Upcoming'),
('Yoga Retreat Weekend', 'Relaxing weekend yoga and meditation retreat', '2025-11-20 10:00:00', '2025-11-22 16:00:00', 30, 250, TRUE, 'Mountain View Retreat Center, CO', 7, 'Upcoming'),
('Hackathon 2025', '48-hour coding competition with amazing prizes', '2025-11-25 09:00:00', '2025-11-27 17:00:00', 150, 0, TRUE, 'Tech Campus, Seattle', 2, 'Upcoming'),
('Book Club Meetup', 'Monthly book discussion group', '2024-09-10 18:00:00', '2024-09-10 20:00:00', 15, 0, FALSE, 'Central Library, Portland', 8, 'Completed'),
('Photography Workshop', 'Learn professional photography techniques', '2025-11-12 10:00:00', '2025-11-12 16:00:00', 25, 60, TRUE, 'Photo Studio, Miami', 3, 'Upcoming'),
('Wine Tasting Event', 'Premium wine tasting with sommelier', '2024-07-05 17:00:00', '2024-07-05 21:00:00', 40, 45, TRUE, 'Vineyard Estate, Napa Valley', 9, 'Completed'),
('Business Leadership Summit', 'Executive leadership and management summit', '2025-01-20 08:00:00', '2025-01-22 17:00:00', 300, 500, TRUE, 'Business Center, Chicago', 4, 'Cancelled'),
('Community Garden Day', 'Volunteer day for community garden maintenance', '2025-11-09 09:00:00', '2025-11-09 15:00:00', 50, 0, TRUE, 'Community Garden, Austin', 10, 'Upcoming'),
('Film Screening & Discussion', 'Independent film screening with Q&A session', '2025-11-16 19:00:00', '2025-11-16 22:00:00', 100, 15, TRUE, 'Indie Cinema, Brooklyn', 5, 'Upcoming');

-- Insert Event Participations (Organizers are auto-added by trigger, adding attendees)
INSERT INTO eventParticipation (eventId, userId, role) VALUES
-- Tech Conference attendees
(1, 2, 'Attendee'),
(1, 3, 'Attendee'),
(1, 4, 'Attendee'),
(1, 5, 'Attendee'),
-- Networking Mixer attendees
(2, 1, 'Attendee'),
(2, 3, 'Attendee'),
(2, 6, 'Attendee'),
-- Art Gallery attendees
(3, 1, 'Attendee'),
(3, 2, 'Attendee'),
(3, 7, 'Attendee'),
(3, 8, 'Attendee'),
-- Marathon Workshop attendees
(4, 1, 'Attendee'),
(4, 5, 'Attendee'),
(4, 9, 'Attendee'),
-- Cooking Class attendees
(5, 2, 'Attendee'),
(5, 6, 'Attendee'),
(5, 10, 'Attendee'),
-- Music Festival attendees
(6, 2, 'Attendee'),
(6, 3, 'Attendee'),
(6, 4, 'Attendee'),
(6, 5, 'Attendee'),
(6, 7, 'Attendee'),
-- Charity Gala attendees
(7, 1, 'Attendee'),
(7, 2, 'Attendee'),
(7, 8, 'Attendee'),
-- Yoga Retreat attendees
(8, 1, 'Attendee'),
(8, 3, 'Attendee'),
(8, 9, 'Attendee'),
-- Hackathon attendees
(9, 1, 'Attendee'),
(9, 4, 'Attendee'),
(9, 5, 'Attendee'),
(9, 10, 'Attendee'),
-- Book Club attendees
(10, 1, 'Attendee'),
(10, 9, 'Attendee'),
-- Photography Workshop attendees
(11, 2, 'Attendee'),
(11, 7, 'Attendee'),
(11, 10, 'Attendee'),
-- Wine Tasting attendees
(12, 1, 'Attendee'),
(12, 4, 'Attendee'),
(12, 6, 'Attendee'),
-- Community Garden attendees
(14, 2, 'Attendee'),
(14, 3, 'Attendee'),
(14, 7, 'Attendee'),
-- Film Screening attendees
(15, 1, 'Attendee'),
(15, 4, 'Attendee'),
(15, 8, 'Attendee');

-- Insert Comments
INSERT INTO comment (content, createdAt, authorId, eventId, parentId) VALUES
('Really excited about this conference! Will there be hands-on workshops?', '2025-10-15 10:30:00', 2, 1, NULL),
('Yes! We will have several workshops on AI and ML topics.', '2025-10-15 11:00:00', 1, 1, 1),
('Great networking opportunity. Looking forward to meeting everyone!', '2025-10-20 14:20:00', 3, 2, NULL),
('The venue looks amazing! Can we bring guests?', '2025-10-25 16:45:00', 1, 3, NULL),
('Unfortunately, this is an invitation-only event.', '2025-10-25 17:30:00', 3, 3, 4),
('What should I bring to the cooking class?', '2025-10-28 09:15:00', 2, 5, NULL),
('Just bring yourself! All ingredients and equipment will be provided.', '2025-10-28 10:00:00', 5, 5, 6),
('Is parking available at the venue?', '2025-10-30 12:00:00', 7, 3, NULL),
('Yes, free parking in the rear lot.', '2025-10-30 13:30:00', 3, 3, 8),
('Will there be food provided during the hackathon?', '2025-11-01 11:20:00', 4, 9, NULL),
('Absolutely! Meals and snacks will be provided throughout the event.', '2025-11-01 12:00:00', 2, 9, 10),
('What skill level is required for the photography workshop?', '2025-11-02 15:45:00', 7, 11, NULL),
('All levels welcome! We will have beginner and advanced tracks.', '2025-11-02 16:30:00', 3, 11, 12),
('Can I volunteer to help with setup?', '2025-11-03 09:00:00', 3, 14, NULL),
('That would be wonderful! Please email us directly.', '2025-11-03 10:15:00', 10, 14, 14);

-- Insert Invitations
INSERT INTO invitation (eventId, userId, confirmation) VALUES
(5, 1, FALSE),
(5, 3, FALSE),
(5, 4, TRUE),
(10, 2, FALSE),
(10, 3, TRUE),
(10, 4, FALSE);

-- Insert Join Requests
INSERT INTO joinRequest (j_status, userId, eventId) VALUES
('Accepted', 6, 2),
('Pending', 7, 5),
('Accepted', 8, 3),
('Rejected', 9, 5),
('Pending', 10, 11);

-- Insert Polls (at least 1 per event)
INSERT INTO poll (title, content, createdAt, eventId) VALUES
('Preferred Workshop Time', 'What time slot works best for the AI workshop?', '2025-10-18 10:00:00', 1),
('Conference Lunch Preference', 'What type of lunch would you prefer?', '2025-10-19 09:00:00', 1),
('Food Preferences', 'Any dietary restrictions we should know about?', '2025-10-22 14:00:00', 2),
('Favorite Art Style', 'Which art style interests you most?', '2025-10-25 10:00:00', 3),
('Follow-up Event Interest', 'Would you attend a follow-up advanced workshop?', '2025-10-10 11:00:00', 4),
('Cuisine Preference', 'Which Italian dish would you like to learn first?', '2025-10-30 14:00:00', 5),
('Skill Level', 'What is your current cooking skill level?', '2025-10-31 10:00:00', 5),
('Favorite Music Genre', 'Which genre are you most excited about?', '2024-08-10 12:00:00', 6),
('Donation Method', 'How would you prefer to donate?', '2025-12-01 15:00:00', 7),
('Entertainment Preference', 'What entertainment would you enjoy most?', '2025-12-02 10:00:00', 7),
('Yoga Style Preference', 'Which yoga style do you prefer?', '2025-11-10 09:00:00', 8),
('T-Shirt Sizes', 'What size t-shirt would you like?', '2025-10-28 09:00:00', 9),
('Programming Language', 'Which programming language will you use?', '2025-10-29 11:00:00', 9),
('Next Book Selection', 'Which book should we read next month?', '2024-09-05 14:00:00', 10),
('Photography Focus', 'What type of photography interests you most?', '2025-11-08 10:00:00', 11),
('Wine Preference', 'Which type of wine do you prefer?', '2024-06-28 16:00:00', 12),
('Session Preference', 'Which leadership topic interests you most?', '2025-01-10 09:00:00', 13),
('Task Preference', 'Which gardening task would you prefer?', '2025-11-05 08:00:00', 14),
('Discussion Format', 'How would you prefer the Q&A session?', '2025-11-12 15:00:00', 15);

-- Insert Poll Options
INSERT INTO pollOption (pollId, optionText) VALUES
(1, 'Morning (9-11 AM)'),
(1, 'Afternoon (2-4 PM)'),
(1, 'Evening (6-8 PM)'),
(2, 'Sandwich & Salad'),
(2, 'Hot Buffet'),
(2, 'Pizza & Pasta'),
(2, 'Healthy Options Only'),
(3, 'Vegetarian'),
(3, 'Vegan'),
(3, 'Gluten-Free'),
(3, 'No Restrictions'),
(4, 'Abstract'),
(4, 'Contemporary'),
(4, 'Classical'),
(4, 'Modern'),
(5, 'Yes, definitely'),
(5, 'Maybe'),
(5, 'No, not interested'),
(6, 'Fresh Pasta'),
(6, 'Risotto'),
(6, 'Tiramisu'),
(6, 'Pizza from scratch'),
(7, 'Beginner'),
(7, 'Intermediate'),
(7, 'Advanced'),
(8, 'Rock'),
(8, 'Electronic'),
(8, 'Indie'),
(8, 'Hip-Hop'),
(8, 'Pop'),
(9, 'Credit Card'),
(9, 'Bank Transfer'),
(9, 'Cash at Event'),
(9, 'Online Platform'),
(10, 'Live Band'),
(10, 'DJ'),
(10, 'Silent Auction'),
(10, 'Comedy Show'),
(11, 'Hatha'),
(11, 'Vinyasa'),
(11, 'Yin'),
(11, 'Restorative'),
(12, 'Small'),
(12, 'Medium'),
(12, 'Large'),
(12, 'X-Large'),
(13, 'Python'),
(13, 'JavaScript'),
(13, 'Java'),
(13, 'Go'),
(13, 'Rust'),
(14, '1984 by George Orwell'),
(14, 'The Great Gatsby'),
(14, 'To Kill a Mockingbird'),
(14, 'Pride and Prejudice'),
(15, 'Portrait'),
(15, 'Landscape'),
(15, 'Street Photography'),
(15, 'Wildlife'),
(16, 'Red Wine'),
(16, 'White Wine'),
(16, 'Rosé'),
(16, 'Sparkling'),
(17, 'Strategic Planning'),
(17, 'Team Management'),
(17, 'Innovation & Change'),
(17, 'Communication Skills'),
(18, 'Planting'),
(18, 'Weeding'),
(18, 'Watering'),
(18, 'Harvesting'),
(19, 'Open Floor Q&A'),
(19, 'Moderated Panel'),
(19, 'Small Group Discussion'),
(19, 'Written Questions Only');

-- Insert Favourite Event
INSERT INTO favouriteEvent (eventId, userId, addedDate) VALUES
(1, 2, '2025-10-16 12:00:00'),
(1, 3, '2025-10-17 14:30:00'),
(2, 1, '2025-10-18 09:00:00'),
(3, 1, '2025-10-20 16:00:00'),
(5, 2, '2025-10-22 11:00:00'),
(7, 1, '2025-10-25 10:30:00'),
(8, 3, '2025-10-26 15:00:00'),
(9, 4, '2025-10-27 13:00:00'),
(11, 7, '2025-10-28 12:00:00'),
(15, 8, '2025-10-29 14:00:00');

-- Insert Reports
INSERT INTO report (userId, description, reportTime, reportType, status) VALUES
(3, 'Event description contains misleading information about speakers', '2025-10-20 15:30:00', 'FraudulentEvent', 'Pending'),
(5, 'Inappropriate language used in event comments section', '2025-10-22 11:20:00', 'ImproperLanguage', 'Accepted'),
(7, 'Spam content posted multiple times in event discussion', '2025-10-25 09:45:00', 'Other', 'Pending'),
(2, 'Event pricing seems misleading - hidden fees not mentioned', '2025-10-28 14:00:00', 'FraudulentEvent', 'Rejected'),
(9, 'Offensive comments directed at other participants', '2025-11-01 10:30:00', 'ImproperLanguage', 'Accepted');

-- Insert Event Reports
INSERT INTO eventReport (reportId, eventId) VALUES
(1, 1),
(4, 7);

-- Insert Comment Reports
INSERT INTO commentReport (reportId, commentId) VALUES
(2, 3),
(5, 10);

-- Insert User Reports
INSERT INTO userReport (reportId, userId) VALUES
(3, 4),
(5, 6);

-- Insert Votes (users upvoting comments)
INSERT INTO vote (userId, commentId) VALUES
(1, 1),
(3, 1),
(4, 1),
(2, 3),
(1, 4),
(2, 6),
(3, 7),
(1, 10),
(5, 10),
(8, 12),
(9, 12),
(2, 14);

-- Insert Uploads
INSERT INTO upload (filePath) VALUES
('/uploads/event/tech_conf_banner.jpg'),
('/uploads/event/networking_poster.png'),
('/uploads/event/art_gallery_flyer.pdf'),
('/uploads/event/marathon_map.jpg'),
('/uploads/event/cooking_menu.pdf'),
('/uploads/profiles/user_avatar_1.jpg'),
('/uploads/profiles/user_avatar_2.jpg'),
('/uploads/documents/event_guidelines.pdf');

-- Insert Notification (some will be created by triggers, adding manual ones)
INSERT INTO notification (userId, title, message, time, isRead, notificationType) VALUES
(1, 'Welcome!', 'Welcome to the event platform! Start exploring events near you.', '2024-01-15 10:35:00', TRUE, 'EventUpdate'),
(2, 'Event Reminder', 'Your event "Tech Conference 2025" starts in 7 days!', '2025-10-25 09:00:00', FALSE, 'EventUpdate'),
(1, 'New Comment', 'Someone commented on your event.', '2025-10-15 11:05:00', TRUE, 'NewComment'),
(1, 'Invitation Received', 'You have been invited to "Cooking Masterclass"', '2025-10-28 10:00:00', FALSE, 'Invitation'),
(6, 'Join Request Approved', 'Your request to join "Startup Networking Mixer" has been approved!', '2025-10-21 14:00:00', TRUE, 'JoinRequest'),
(3, 'New Poll', 'A new poll has been created for "Tech Conference 2025"', '2025-10-18 10:05:00', FALSE, 'PollCreated'),
(5, 'Report Update', 'Your report has been reviewed by our team.', '2025-10-26 11:00:00', TRUE, 'ReportStatus');

-- Insert Event Notification
INSERT INTO eventNotification (notificationId, eventId) VALUES
(2, 1),
(3, 1),
(4, 5),
(5, 2),
(6, 1);

INSERT INTO eventCategory (eventId, categoryId) VALUES
(1, 1),
(2, 9),
(3, 3),
(4, 4),
(4, 6),
(5, 5),
(5, 8),
(6, 7),
(7, 5),
(7, 10),
(8, 6),
(9, 1),
(10, 3),
(10, 5),
(11, 3),
(11, 5),
(12, 8),
(13, 2),
(14, 10),
(15, 3),
(15, 5);

INSERT INTO eventTag(eventId, tagId) VALUES
(1, 2),
(1, 3),
(1, 10),
(2 , 4),
(2, 1),
(3, 7),
(4, 1),
(4, 8),
(5, 1),
(6, 5),
(7, 4),
(8, 1),
(8, 8),
(9, 9),
(9, 6),
(10, 4),
(11, 1),
(11, 8),
(12, 4),
(13, 2),
(13, 3),
(14, 4),
(15, 7),
(15, 4);


-- Display summary
SELECT 'Database populated successfully!' AS status;
SELECT COUNT(*) AS total_users FROM "user";
SELECT COUNT(*) AS total_events FROM event;
SELECT COUNT(*) AS total_comments FROM comment;
SELECT COUNT(*) AS total_participations FROM eventParticipation;
SELECT COUNT(*) AS total_notifications FROM notification;