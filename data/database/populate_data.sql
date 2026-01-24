-- ========================================================
-- ZCOM Studios Unified Data Population Script
-- ========================================================
-- Description: Inserts admin user, tags, and articles into the database.
-- Version: 2.0
-- ========================================================

USE jdwxjwte_zcom_db;

-- ========================================================
-- 1. Insert Admin User
-- ========================================================
-- IMPORTANT: Change this password immediately after first login!
INSERT INTO `admin_users` (`username`, `email`, `password_hash`) 
VALUES ('admin', 'admin@zcomstudios.com', '$2y$10$PYmiUaZCGm4zJexXBP4LL.PuWwbn6IFUWXsKZiHMMUDObRiHD5kAa')
ON DUPLICATE KEY UPDATE username=username;

-- ========================================================
-- 2. Populate Tags Registry
-- ========================================================
-- Insert all unique tags from the articles into the registry
INSERT IGNORE INTO `tags_registry` (display_name, slug) VALUES
('AI', 'ai'),
('Software Development', 'software-development'),
('Coding Tools', 'coding-tools'),
('Developer Productivity', 'developer-productivity'),
('Machine Learning', 'machine-learning'),
('Future of Work', 'future-of-work'),
('Programming', 'programming'),
('Tech Trends', 'tech-trends'),
('Productivity', 'productivity'),
('Email Management', 'email-management'),
('Gaming', 'gaming'),
('Workflow', 'workflow'),
('Time Management', 'time-management'),
('Design Systems', 'design-systems'),
('UI/UX', 'ui-ux'),
('Component Architecture', 'component-architecture'),
('Figma', 'figma'),
('React', 'react');

-- ========================================================
-- 3. Insert Articles
-- ========================================================

-- Article 1: The Developer-AI Handshake
INSERT INTO `articles` (
    article_id, title, slug, author_name, author_role,
    published_date, status, category, excerpt,
    content, word_count, reading_time, featured, visibility
) VALUES (
    'article_0a01',
    'The Developer-AI Handshake: Why the Best Engineers in 2026 Will Be Collaborators, Not Coders',
    'developer-ai-handshake-2026',
    'Developer',
    'Software Engineer & Writer',
    '2026-01-15 12:00:00',
    'published',
    'Technology',
    'While AI tool adoption soars, developer trust is plummeting. The paradox reveals a new truth about what it takes to thrive in software development.',
    '<p>The numbers are in, and they tell a surprising story...</p>',
    2847,
    11,
    1,
    'public'
) ON DUPLICATE KEY UPDATE title=VALUES(title);

SET @article1_id = (SELECT id FROM articles WHERE slug = 'developer-ai-handshake-2026');

-- Article 2: The Loot Filter Method
INSERT INTO `articles` (
    article_id, title, slug, author_name, author_role,
    published_date, status, category, excerpt,
    content, word_count, reading_time, featured, visibility
) VALUES (
    'article_0a02',
    'The Loot Filter Method: Control Your Inbox Like a Pro',
    'loot-filter-inbox-method',
    'Developer',
    'Software Engineer & Writer',
    '2026-01-02 12:00:00',
    'published',
    'Productivity',
    'Inbox overload is a design problem. Treating email as an urgent real-time stream trains you to react rather than focus. Apply the gamers loot filter approach to email.',
    '<p>Inbox overload is a design problem...</p>',
    850,
    4,
    0,
    'public'
) ON DUPLICATE KEY UPDATE title=VALUES(title);

SET @article2_id = (SELECT id FROM articles WHERE slug = 'loot-filter-inbox-method');

-- Article 3: Graphic Design Is Code
INSERT INTO `articles` (
    article_id, title, slug, author_name, author_role,
    published_date, status, category, excerpt,
    content, word_count, reading_time, featured, visibility
) VALUES (
    'article_0a03',
    'Graphic Design Is Code: Build Interfaces as Reusable Systems',
    'graphic-design-is-code',
    'Developer',
    'Software Engineer & Writer',
    '2025-12-28 12:00:00',
    'published',
    'Design & Engineering',
    'Whether your components live in Figma or React, treating UI as a system speeds delivery and reduces rework. Components are contracts—clear inputs and predictable outputs.',
    '<p>Whether your components live in Figma or React, treating UI as a system speeds delivery and reduces rework...</p>',
    920,
    4,
    1,
    'public'
) ON DUPLICATE KEY UPDATE title=VALUES(title);

SET @article3_id = (SELECT id FROM articles WHERE slug = 'graphic-design-is-code');

-- ========================================================
-- 4. Link Tags to Articles
-- ========================================================

-- Tags for Article 1
INSERT IGNORE INTO `article_tags` (article_id, tag_id) SELECT @article1_id, id FROM `tags_registry` WHERE slug IN ('ai', 'software-development', 'coding-tools', 'developer-productivity', 'machine-learning', 'future-of-work', 'programming', 'tech-trends');

-- Tags for Article 2
INSERT IGNORE INTO `article_tags` (article_id, tag_id) SELECT @article2_id, id FROM `tags_registry` WHERE slug IN ('productivity', 'email-management', 'gaming', 'workflow', 'time-management');

-- Tags for Article 3
INSERT IGNORE INTO `article_tags` (article_id, tag_id) SELECT @article3_id, id FROM `tags_registry` WHERE slug IN ('design-systems', 'ui-ux', 'component-architecture', 'figma', 'react');
