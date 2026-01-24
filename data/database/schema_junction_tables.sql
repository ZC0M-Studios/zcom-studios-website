-- ========================================================
-- ZCOM Studios - Junction Tables for Tag Relationships
-- ========================================================
-- Description: Creates junction tables for many-to-many tag relationships
-- Version: 1.0
-- Run after schema.sql
-- ========================================================

USE jdwxjwte_zcom_db;

-- ========================================================
-- Project-Tags Junction Table
-- ========================================================
CREATE TABLE IF NOT EXISTS `project_tags` (
  `project_id` INT(11) UNSIGNED NOT NULL,
  `tag_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`project_id`, `tag_id`),
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tag_id`) REFERENCES `tags_registry`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- Prompt-Tags Junction Table
-- ========================================================
CREATE TABLE IF NOT EXISTS `prompt_tags` (
  `prompt_id` INT(11) UNSIGNED NOT NULL,
  `tag_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`prompt_id`, `tag_id`),
  FOREIGN KEY (`prompt_id`) REFERENCES `prompts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tag_id`) REFERENCES `tags_registry`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- Tool-Tags Junction Table
-- ========================================================
CREATE TABLE IF NOT EXISTS `tool_tags` (
  `tool_id` INT(11) UNSIGNED NOT NULL,
  `tag_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`tool_id`, `tag_id`),
  FOREIGN KEY (`tool_id`) REFERENCES `tools`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tag_id`) REFERENCES `tags_registry`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- Project Tech Stack Table (Extended metadata)
-- ========================================================
CREATE TABLE IF NOT EXISTS `project_tech_stack` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` INT(11) UNSIGNED NOT NULL,
  `category` ENUM('frontend','backend','database','tools','frameworks','languages') NOT NULL,
  `technology` VARCHAR(100) NOT NULL,
  `display_order` INT(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_project_id` (`project_id`),
  INDEX `idx_category` (`category`),
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- Project Features Table
-- ========================================================
CREATE TABLE IF NOT EXISTS `project_features` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` INT(11) UNSIGNED NOT NULL,
  `feature` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `display_order` INT(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_project_id` (`project_id`),
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- Project Links Table
-- ========================================================
CREATE TABLE IF NOT EXISTS `project_links` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` INT(11) UNSIGNED NOT NULL,
  `link_type` ENUM('live_url','github','demo','documentation','case_study','other') NOT NULL,
  `url` VARCHAR(500) NOT NULL,
  `label` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_project_id` (`project_id`),
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- Project Media Table
-- ========================================================
CREATE TABLE IF NOT EXISTS `project_media` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` INT(11) UNSIGNED NOT NULL,
  `media_type` ENUM('thumbnail','screenshot','video','gif','other') NOT NULL,
  `url` VARCHAR(500) NOT NULL,
  `alt_text` VARCHAR(255) DEFAULT NULL,
  `caption` TEXT,
  `display_order` INT(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_project_id` (`project_id`),
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

