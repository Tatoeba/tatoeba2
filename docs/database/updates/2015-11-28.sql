-- Add id column since multiple sentence_id may exist
-- for different languages when changing sentence flag
ALTER TABLE reindex_flags
    DROP PRIMARY KEY,
    ADD id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
    ADD KEY `idx_sentence_id` (`sentence_id`);
