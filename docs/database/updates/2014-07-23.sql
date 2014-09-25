ALTER TABLE sentences_translations ADD id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;

-- Remove the auto_increment flag by redefining the column
-- just like it was but without this flag
ALTER TABLE tags_sentences CHANGE tag_id tag_id int(11) NOT NULL;

ALTER TABLE tags_sentences ADD id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
