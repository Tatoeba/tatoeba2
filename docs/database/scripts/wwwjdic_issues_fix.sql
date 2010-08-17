-- Delete sentences with indices linked to a deleted sentence
DELETE sa.*
FROM sentence_annotations sa
  LEFT JOIN sentences s ON sa.sentence_id = s.id
WHERE s.id IS NULL;


-- Update sentence_lang and translation_lang fields. We'll need it below.
UPDATE sentences_translations st, sentences s
SET st.sentence_lang = s.lang
WHERE st.sentence_id = s.id AND st.sentence_lang IS NULL;

UPDATE sentences_translations st, sentences s
SET st.translation_lang = s.lang
WHERE st.translation_id = s.id AND st.translation_lang IS NULL;


-- Fix annotations with meaning_id mismatch, where there is an English sentence.
UPDATE sentence_annotations sa, sentences_translations st 
SET sa.meaning_id = st.translation_id, sa.user_id = 5, sa.modified = NOW()
WHERE sa.meaning_id > 0
AND sa.sentence_id = st.sentence_id 
AND st.translation_lang = 'eng'
AND (sa.sentence_id, sa.meaning_id) NOT IN 
(
  SELECT st.sentence_id, st.translation_id
  FROM sentences_translations st
);


-- Fix annotations with meaning_id mismatch, where there is no English sentence.
UPDATE sentence_annotations sa
SET sa.meaning_id = 0, sa.user_id = 5, sa.modified = NOW()
WHERE sa.meaning_id != -1
AND (sa.sentence_id, sa.meaning_id) NOT IN 
(
  SELECT st.sentence_id, st.translation_id
  FROM sentences_translations st
);