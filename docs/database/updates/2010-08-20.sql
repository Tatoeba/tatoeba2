-- add number of sentences for each tag
ALTER TABLE tags ADD COLUMN nbrOfSentences INT NOT NULL DEFAULT 0;
CREATE INDEX nbr_sentences_idx ON tags(nbrOfSentences) ;
-- also run  scripts/maintain_tags_number_of_sentences.sql
-- and  scripts/create_nbr_sentences_of_tags.sql

