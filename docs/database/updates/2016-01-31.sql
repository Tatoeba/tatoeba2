-- clean up ghost transcriptions, there's only one that needs to be remapped but it already has a duplicate so yeah
DELETE t FROM transcriptions t
LEFT JOIN sentences s
ON t.sentence_id = s.id
WHERE s.id IS NULL;

-- get rid of the broken and useless lang_id fields
ALTER TABLE reindex_flags CHANGE COLUMN lang_id lang varchar(4) DEFAULT NULL;
UPDATE reindex_flags r
  JOIN languages l
  ON l.id = r.lang
  SET r.lang = l.code;
ALTER TABLE sentences DROP COLUMN lang_id;
