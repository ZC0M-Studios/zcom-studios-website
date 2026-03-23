-- =============================================================================
-- Ant-IO: Soul Index Schema
-- Database: jdwxjwte_zcom_db
-- Description: Blockchain-like immutable append-only ledger for ant colony
--              genetic algorithm generations and individual genome storage.
-- Run: SOURCE /path/to/schema_ant_io.sql;
-- =============================================================================

USE jdwxjwte_zcom_db;

-- ANCHOR:SOUL_INDEX
-- The immutable chain of generation snapshots.
-- RULE: No UPDATE or DELETE is ever issued against this table by the application.
--       Only INSERT. Integrity is enforced by the prev_hash → block_hash chain.
-- Genesis block uses prev_hash = '0000...0000' (64 zeros).
-- block_hash = SHA256(prev_hash || generation || population fields || fitness fields || created_at_unix)
CREATE TABLE IF NOT EXISTS `soul_index` (
  `id`              INT UNSIGNED      NOT NULL AUTO_INCREMENT,
  `block_index`     INT UNSIGNED      NOT NULL,
  `generation`      INT UNSIGNED      NOT NULL,
  `colony_0_pop`    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `colony_1_pop`    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `best_fitness_c0` FLOAT             NOT NULL DEFAULT 0,
  `best_fitness_c1` FLOAT             NOT NULL DEFAULT 0,
  `avg_fitness_c0`  FLOAT             NOT NULL DEFAULT 0,
  `avg_fitness_c1`  FLOAT             NOT NULL DEFAULT 0,
  `find_rate`       FLOAT             NOT NULL DEFAULT 0 COMMENT 'Avg food finds per tick this gen',
  `kill_rate`       FLOAT             NOT NULL DEFAULT 0 COMMENT 'Avg enemy kills per tick this gen',
  `dmg_recv_rate`   FLOAT             NOT NULL DEFAULT 0 COMMENT 'Avg damage received per tick this gen',
  `prev_hash`       CHAR(64)          NOT NULL COMMENT 'block_hash of the previous block',
  `block_hash`      CHAR(64)          NOT NULL COMMENT 'SHA256 of all fields + prev_hash',
  `created_at`      TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_block_index` (`block_index`),
  UNIQUE KEY `uq_block_hash` (`block_hash`),
  INDEX `idx_generation` (`generation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Immutable blockchain-like generation ledger. Append-only.';

-- ANCHOR:ANT_GENOMES
-- Individual ant genomes, stored per Soul block.
-- genome_id = SHA256(parent_a_id || parent_b_id || generation || random_seed_hex)
-- Genesis ants use literal string "NULL" for both parent slots in the hash input.
-- nn_weights_b64: Base64 of Float32Array in row-major order [W1, b1, W2, b2]
-- traits_json: JSON object of scalar genetic trait values
CREATE TABLE IF NOT EXISTS `ant_genomes` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `block_index`    INT UNSIGNED NOT NULL,
  `genome_id`      CHAR(64)     NOT NULL COMMENT 'SHA256-derived unique genome identifier',
  `colony_id`      TINYINT UNSIGNED NOT NULL COMMENT '0 = colony A (red), 1 = colony B (blue)',
  `generation`     INT UNSIGNED NOT NULL,
  `parent_a_id`    CHAR(64)     NULL     COMMENT 'genome_id of first parent; NULL for genesis',
  `parent_b_id`    CHAR(64)     NULL     COMMENT 'genome_id of second parent; NULL for genesis',
  `nn_weights_b64` MEDIUMTEXT   NOT NULL COMMENT 'Base64-encoded Float32Array of NN weights',
  `traits_json`    JSON         NOT NULL COMMENT 'Genetic scalar traits object',
  `fitness_score`  FLOAT        NOT NULL DEFAULT 0,
  `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_genome_id` (`genome_id`),
  INDEX `idx_block` (`block_index`),
  INDEX `idx_colony_gen` (`colony_id`, `generation`),
  CONSTRAINT `fk_genome_block`
    FOREIGN KEY (`block_index`) REFERENCES `soul_index` (`block_index`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Per-ant genome storage linked to soul_index blocks.';
