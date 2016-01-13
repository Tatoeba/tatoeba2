ALTER TABLE `sentences_lists` ADD `visibility` enum('private', 'unlisted', 'public') NOT NULL DEFAULT 'unlisted';
ALTER TABLE `sentences_lists` ADD `editable_by` enum('creator', 'anyone') NOT NULL DEFAULT 'creator';

UPDATE sentences_lists SET visibility = 'public' WHERE is_public = 1;
UPDATE sentences_lists SET editable_by = 'anyone' WHERE is_public = 1;