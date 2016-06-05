ALTER TABLE sentences_lists MODIFY COLUMN editable_by enum('creator', 'anyone', 'no_one') NOT NULL DEFAULT 'creator';
