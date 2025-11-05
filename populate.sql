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
DELETE FROM notifications;
DELETE FROM upload;
DELETE FROM events;
DELETE FROM tag;
DELETE FROM category;
DELETE FROM appAdmin;
DELETE FROM "user";

-- Reset sequences
ALTER SEQUENCE user_userid_seq RESTART WITH 1;
ALTER SEQUENCE events_eventid_seq RESTART WITH 1;
ALTER SEQUENCE comment_commentid_seq RESTART WITH 1;
ALTER SEQUENCE notifications_notificationid_seq RESTART WITH 1;
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
('john_doe', 'john.doe@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '/images/profiles/john.jpg', '2024-01-15 10:30:00', FALSE),
('jane_smith', 'jane.smith@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '/images/profiles/jane.jpg', '2024-02-20 14:20:00', FALSE),
('mike_wilson', 'mike.wilson@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '/images/profiles/mike.jpg', '2024-03-10 09:15:00', FALSE),
('sarah_jones', 'sarah.jones@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '/images/profiles/sarah.jpg', '2024-03-25 16:45:00', FALSE),
('david_brown', 'david.brown@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '/images/profiles/david.jpg', '2024-04-05 11:00:00', FALSE),
('emily_davis', 'emily.davis@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '/images/profiles/emily.jpg', '2024-04-18 13:30:00', FALSE),
('alex_martin', 'alex.martin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '/images/profiles/alex.jpg', '2024-05-02 10:00:00', FALSE),
('lisa_garcia', 'lisa.garcia@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '/images/profiles/lisa.jpg', '2024-05-20 15:20:00', FALSE),
('tom_rodriguez', 'tom.rodriguez@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '/images/profiles/tom.jpg', '2024-06-01 12:10:00', FALSE),
('anna_lee', 'anna.lee@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '/images/profiles/anna.jpg', '2024-06-15 14:50:00', FALSE),
('admin_user', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '/images/profiles/admin.jpg', '2024-01-01 08:00:00', TRUE);

-- Insert App Admins
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

-- Insert Events (mix of Upcoming, Ongoing, Completed, Cancelled)
INSERT INTO events (eventName, description, startDate, endDate, capacity, price, isPublic, location, createdBy, eventStatus) VALUES
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

-- Insert Polls
INSERT INTO poll (title, content, createdAt, eventId) VALUES
('Preferred Workshop Time', 'What time slot works best for the AI workshop?', '2025-10-18 10:00:00', 1),
('Food Preferences', 'Any dietary restrictions we should know about?', '2025-10-22 14:00:00', 2),
('T-Shirt Sizes', 'What size t-shirt would you like?', '2025-10-28 09:00:00', 9),
('Follow-up Event Interest', 'Would you attend a follow-up advanced workshop?', '2025-10-30 11:00:00', 4);

-- Insert Poll Options (Note: triggers will auto-add Yes/No, adding custom options)
INSERT INTO pollOption (pollId, optionText) VALUES
(1, 'Morning (9-11 AM)'),
(1, 'Afternoon (2-4 PM)'),
(1, 'Evening (6-8 PM)'),
(2, 'Vegetarian'),
(2, 'Vegan'),
(2, 'Gluten-Free'),
(2, 'No Restrictions'),
(3, 'Small'),
(3, 'Medium'),
(3, 'Large'),
(3, 'X-Large');

-- Insert Favourite Events
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
('/uploads/events/tech_conf_banner.jpg'),
('/uploads/events/networking_poster.png'),
('/uploads/events/art_gallery_flyer.pdf'),
('/uploads/events/marathon_map.jpg'),
('/uploads/events/cooking_menu.pdf'),
('/uploads/profiles/user_avatar_1.jpg'),
('/uploads/profiles/user_avatar_2.jpg'),
('/uploads/documents/event_guidelines.pdf');

-- Insert Notifications (some will be created by triggers, adding manual ones)
INSERT INTO notifications (title, message, time, isRead, notificationType) VALUES
('Welcome!', 'Welcome to the event platform! Start exploring events near you.', '2024-01-15 10:35:00', TRUE, 'EventUpdate'),
('Event Reminder', 'Your event "Tech Conference 2025" starts in 7 days!', '2025-10-25 09:00:00', FALSE, 'EventUpdate'),
('New Comment', 'Someone commented on your event.', '2025-10-15 11:05:00', TRUE, 'NewComment'),
('Invitation Received', 'You have been invited to "Cooking Masterclass"', '2025-10-28 10:00:00', FALSE, 'Invitation'),
('Join Request Approved', 'Your request to join "Startup Networking Mixer" has been approved!', '2025-10-21 14:00:00', TRUE, 'JoinRequest'),
('New Poll', 'A new poll has been created for "Tech Conference 2025"', '2025-10-18 10:05:00', FALSE, 'PollCreated'),
('Report Update', 'Your report has been reviewed by our team.', '2025-10-26 11:00:00', TRUE, 'ReportStatus');

-- Insert Event Notifications
INSERT INTO eventNotification (notificationId, eventId) VALUES
(2, 1),
(3, 1),
(4, 5),
(5, 2),
(6, 1);

-- Display summary
SELECT 'Database populated successfully!' AS status;
SELECT COUNT(*) AS total_users FROM "user";
SELECT COUNT(*) AS total_events FROM events;
SELECT COUNT(*) AS total_comments FROM comment;
SELECT COUNT(*) AS total_participations FROM eventParticipation;
SELECT COUNT(*) AS total_notifications FROM notifications;