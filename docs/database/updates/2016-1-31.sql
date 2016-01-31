-- clean up ghost transcriptions, there's only one that needs to be remapped but it already has a duplicate so yeah
DELETE t FROM transcriptions t
LEFT JOIN sentences s
ON t.sentence_id = s.id
WHERE s.id IS NULL;
