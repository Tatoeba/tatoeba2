-- Remove from lists sentences that have been deleted.
DELETE FROM `sentences_sentences_lists` 
WHERE sentence_id NOT IN (SELECT id FROM sentences);

-- Update number of sentences
UPDATE sentences_lists l SET numberOfSentences = (
    SELECT count(*) FROM `sentences_sentences_lists` 
    WHERE sentences_list_id = l.id 
);