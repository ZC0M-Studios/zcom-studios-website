-- ========================================================
-- ZCOM Studios - Tags Display Styling Columns
-- ========================================================
-- Description: Adds display styling columns to tags_registry table
-- Version: 1.1
-- Run after schema.sql and schema_junction_tables.sql
-- ========================================================

USE jdwxjwte_zcom_db;

-- ========================================================
-- Add Display Styling Columns to tags_registry
-- ========================================================
-- Note: Run each statement separately if column already exists

ALTER TABLE `tags_registry`
ADD COLUMN `text_color` VARCHAR(20) DEFAULT '#ffffff' AFTER `description`,
ADD COLUMN `bg_color` VARCHAR(20) DEFAULT '#333333' AFTER `text_color`,
ADD COLUMN `border_color` VARCHAR(20) DEFAULT '#666666' AFTER `bg_color`,
ADD COLUMN `border_type` ENUM('solid','dashed','dotted','double','none') DEFAULT 'solid' AFTER `border_color`,
ADD COLUMN `shadow_color` VARCHAR(20) DEFAULT NULL AFTER `border_type`;

-- ========================================================
-- Update existing default tag styling by category
-- ========================================================
-- Technology tags: Blue theme
UPDATE `tags_registry` 
SET text_color = '#ffffff', bg_color = '#0066cc', border_color = '#0099ff', shadow_color = 'rgba(0,153,255,0.3)'
WHERE category = 'technology' AND (text_color IS NULL OR text_color = '#ffffff');

-- Language tags: Green theme
UPDATE `tags_registry` 
SET text_color = '#ffffff', bg_color = '#00994d', border_color = '#00cc52', shadow_color = 'rgba(0,204,82,0.3)'
WHERE category = 'language' AND (text_color IS NULL OR text_color = '#ffffff');

-- Framework tags: Orange theme
UPDATE `tags_registry` 
SET text_color = '#ffffff', bg_color = '#cc5500', border_color = '#ff6b00', shadow_color = 'rgba(255,107,0,0.3)'
WHERE category = 'framework' AND (text_color IS NULL OR text_color = '#ffffff');

-- Tool tags: Purple theme
UPDATE `tags_registry` 
SET text_color = '#ffffff', bg_color = '#6600cc', border_color = '#9933ff', shadow_color = 'rgba(153,51,255,0.3)'
WHERE category = 'tool' AND (text_color IS NULL OR text_color = '#ffffff');

-- Topic tags: Yellow/amber theme
UPDATE `tags_registry` 
SET text_color = '#000000', bg_color = '#cc8800', border_color = '#ffaa00', shadow_color = 'rgba(255,170,0,0.3)'
WHERE category = 'topic' AND (text_color IS NULL OR text_color = '#ffffff');

-- Skill tags: Teal theme
UPDATE `tags_registry` 
SET text_color = '#ffffff', bg_color = '#009980', border_color = '#00cc99', shadow_color = 'rgba(0,204,153,0.3)'
WHERE category = 'skill' AND (text_color IS NULL OR text_color = '#ffffff');

-- Industry tags: Magenta theme
UPDATE `tags_registry` 
SET text_color = '#ffffff', bg_color = '#990052', border_color = '#cc0066', shadow_color = 'rgba(204,0,102,0.3)'
WHERE category = 'industry' AND (text_color IS NULL OR text_color = '#ffffff');

