-- Create database schema for event management application

-- Enable UUID extension if not already enabled
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Table R01: user
CREATE TABLE "user" (
    userId UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profileImage TEXT,
    registrationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    isAdmin BOOLEAN DEFAULT FALSE NOT NULL
);

-- Table R02: event
CREATE TABLE event (
    eventId UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    title VARCHAR(255) NOT NULL,
    description TEXT,
    startDate TIMESTAMP NOT NULL,
    endDate TIMESTAMP,
    capacity INTEGER,
    isPaid BOOLEAN DEFAULT FALSE NOT NULL,
    isPublic BOOLEAN DEFAULT TRUE NOT NULL,
    location TEXT,
    createdBy UUID NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (createdBy) REFERENCES "user"(userId) ON DELETE CASCADE
);

-- Table R03: comment
CREATE TABLE comment (
    commentId UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    content TEXT NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    authorId UUID NOT NULL,
    eventId UUID NOT NULL,
    FOREIGN KEY (authorId) REFERENCES "user"(userId) ON DELETE CASCADE,
    FOREIGN KEY (eventId) REFERENCES event(eventId) ON DELETE CASCADE
);

-- Table R04: event_participation
CREATE TABLE event_participation (
    userId UUID NOT NULL,
    eventId UUID NOT NULL,
    role VARCHAR(50) NOT NULL CHECK (role IN ('ATTENDEE', 'ORGANIZER', 'SPEAKER', 'VOLUNTEER')),
    participationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    PRIMARY KEY (userId, eventId),
    FOREIGN KEY (userId) REFERENCES "user"(userId) ON DELETE CASCADE,
    FOREIGN KEY (eventId) REFERENCES event(eventId) ON DELETE CASCADE
);

-- Table R05: report (adicionei esta tabela baseado na nossa discussão anterior)
CREATE TABLE report (
    reportId UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    description TEXT NOT NULL,
    reportDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    reportType VARCHAR(50) NOT NULL CHECK (reportType IN ('USER_BEHAVIOR', 'EVENT_ISSUE', 'SAFETY_CONCERN', 'CONTENT_VIOLATION')),
    status VARCHAR(20) DEFAULT 'PENDING' CHECK (status IN ('PENDING', 'RESOLVED', 'DISMISSED')),
    severityLevel VARCHAR(20) DEFAULT 'MEDIUM' CHECK (severityLevel IN ('LOW', 'MEDIUM', 'HIGH', 'CRITICAL')),
    reportedBy UUID NOT NULL,
    reportedUser UUID,
    reportedEvent UUID,
    FOREIGN KEY (reportedBy) REFERENCES "user"(userId) ON DELETE CASCADE,
    FOREIGN KEY (reportedUser) REFERENCES "user"(userId) ON DELETE SET NULL,
    FOREIGN KEY (reportedEvent) REFERENCES event(eventId) ON DELETE SET NULL,
    CONSTRAINT valid_report CHECK (
        reportedUser IS NOT NULL OR reportedEvent IS NOT NULL
    )
);

-- Create indexes for better performance
CREATE INDEX idx_event_createdBy ON event(createdBy);
CREATE INDEX idx_event_startDate ON event(startDate);
CREATE INDEX idx_comment_eventId ON comment(eventId);
CREATE INDEX idx_comment_authorId ON comment(authorId);
CREATE INDEX idx_event_participation_userId ON event_participation(userId);
CREATE INDEX idx_event_participation_eventId ON event_participation(eventId);
CREATE INDEX idx_report_reportedBy ON report(reportedBy);
CREATE INDEX idx_report_status ON report(status);