-- add support for audio in database --

ALTER TABLE `sentences` ADD COLUMN `hasaudio` ENUM('no','from_users', 'shtooka') NOT NULL DEFAULT 'no';
CREATE INDEX hasaudio_idx ON sentences (hasaudio) ;
