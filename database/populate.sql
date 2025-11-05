-- Insert sample data for event management application

-- Insert users
INSERT INTO "user" (userId, username, email, password, isAdmin) VALUES
    ('11111111-1111-1111-1111-111111111111', 'admin_user', 'admin@eventapp.com', 'hashed_password_1', TRUE),
    ('22222222-2222-2222-2222-222222222222', 'john_doe', 'john@example.com', 'hashed_password_2', FALSE),
    ('33333333-3333-3333-3333-333333333333', 'jane_smith', 'jane@example.com', 'hashed_password_3', FALSE),
    ('44444444-4444-4444-4444-444444444444', 'bob_wilson', 'bob@example.com', 'hashed_password_4', FALSE),
    ('55555555-5555-5555-5555-555555555555', 'alice_brown', 'alice@example.com', 'hashed_password_5', FALSE);

-- Insert events
INSERT INTO event (eventId, title, description, startDate, endDate, capacity, isPaid, isPublic, location, createdBy) VALUES
    ('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', 'Tech Conference 2024', 'Annual technology conference featuring the latest innovations', '2024-06-15 09:00:00', '2024-06-16 18:00:00', 500, TRUE, TRUE, 'Convention Center, Lisbon', '22222222-2222-2222-2222-222222222222'),
    ('bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb', 'Summer Music Festival', 'Open air music festival with various artists', '2024-07-20 14:00:00', '2024-07-21 23:00:00', 1000, FALSE, TRUE, 'City Park, Porto', '33333333-3333-3333-3333-333333333333'),
    ('cccccccc-cccc-cccc-cccc-cccccccccccc', 'Private Coding Workshop', 'Exclusive hands-on coding workshop', '2024-05-10 10:00:00', '2024-05-10 16:00:00', 20, FALSE, FALSE, 'Tech Hub, Coimbra', '22222222-2222-2222-2222-222222222222'),
    ('dddddddd-dddd-dddd-dddd-dddddddddddd', 'Art Exhibition Opening', 'Contemporary art exhibition opening night', '2024-08-05 18:00:00', '2024-08-05 22:00:00', 100, TRUE, TRUE, 'Modern Art Museum, Lisbon', '44444444-4444-4444-4444-444444444444');

-- Insert event participations
INSERT INTO event_participation (userId, eventId, role) VALUES
    ('22222222-2222-2222-2222-222222222222', 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', 'ORGANIZER'),
    ('33333333-3333-3333-3333-333333333333', 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', 'SPEAKER'),
    ('44444444-4444-4444-4444-444444444444', 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', 'ATTENDEE'),
    ('55555555-5555-5555-5555-555555555555', 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', 'ATTENDEE'),
    ('33333333-3333-3333-3333-333333333333', 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb', 'ORGANIZER'),
    ('22222222-2222-2222-2222-222222222222', 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb', 'ATTENDEE'),
    ('44444444-4444-4444-4444-444444444444', 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb', 'VOLUNTEER'),
    ('44444444-4444-4444-4444-444444444444', 'dddddddd-dddd-dddd-dddd-dddddddddddd', 'ORGANIZER'),
    ('55555555-5555-5555-5555-555555555555', 'dddddddd-dddd-dddd-dddd-dddddddddddd', 'ATTENDEE');

-- Insert comments
INSERT INTO comment (commentId, content, authorId, eventId) VALUES
    (uuid_generate_v4(), 'Looking forward to this conference! The speaker lineup looks amazing.', '44444444-4444-4444-4444-444444444444', 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa'),
    (uuid_generate_v4(), 'What specific technologies will be covered in the workshops?', '33333333-3333-3333-3333-333333333333', 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa'),
    (uuid_generate_v4(), 'The venue for the music festival is perfect for summer events!', '22222222-2222-2222-2222-222222222222', 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb'),
    (uuid_generate_v4(), 'Is there parking available near the convention center?', '55555555-5555-5555-5555-555555555555', 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa');

-- Insert reports
INSERT INTO report (reportId, description, reportType, severityLevel, reportedBy, reportedUser, reportedEvent) VALUES
    (uuid_generate_v4(), 'User was disruptive during the presentation', 'USER_BEHAVIOR', 'MEDIUM', '11111111-1111-1111-1111-111111111111', '44444444-4444-4444-4444-444444444444', 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa'),
    (uuid_generate_v4(), 'Event location changed without proper notification', 'EVENT_ISSUE', 'LOW', '22222222-2222-2222-2222-222222222222', NULL, 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb');

-- Display creation summary
SELECT 
    (SELECT COUNT(*) FROM "user") as user_count,
    (SELECT COUNT(*) FROM event) as event_count,
    (SELECT COUNT(*) FROM event_participation) as participation_count,
    (SELECT COUNT(*) FROM comment) as comment_count,
    (SELECT COUNT(*) FROM report) as report_count;