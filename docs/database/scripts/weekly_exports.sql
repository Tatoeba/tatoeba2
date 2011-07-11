-- Files that are exported every week on Saturday, at 9AM.

-- WWWJDIC indices (also called "B lines")
SELECT sentence_id, meaning_id, text FROM `sentence_annotations` 
INTO OUTFILE '/var/tmp/jpn_indices.csv';
  
-- Sentences
SELECT id, lang, text FROM `sentences`
INTO OUTFILE '/var/tmp/sentences.csv';

-- Sentences with more data
SELECT s.id, s.lang, s.text, u.username, s.created, s.modified 
FROM `sentences` s LEFT JOIN `users` u ON s.user_id = u.id
INTO OUTFILE '/var/tmp/sentences_detailed.csv';

-- Links between sentences
SELECT sentence_id, translation_id FROM `sentences_translations`
INTO OUTFILE '/var/tmp/links.csv';

-- Sentences tags
SELECT ts.sentence_id, t.name FROM `tags` t JOIN `tags_sentences` ts
  ON t.id = ts.tag_id
INTO OUTFILE '/var/tmp/tags.csv';