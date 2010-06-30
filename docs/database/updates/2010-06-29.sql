-- add support for audio in database --

ALTER TABLE `sentences` ADD COLUMN `hasaudio` ENUM('no','from_users', 'shtooka') NOT NULL DEFAULT 'no';

