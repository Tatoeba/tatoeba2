-- Make sure there's no empty string but null for unsupported languages
UPDATE sentences_translations
SET sentence_lang = null WHERE sentence_lang = '';

UPDATE sentences_translations
SET translation_lang = null WHERE translation_lang = '';

-- Update sentence_lang and translation_lang fields where lang is null.
UPDATE sentences_translations st, sentences s
SET st.sentence_lang = s.lang
WHERE st.sentence_id = s.id AND st.sentence_lang IS NULL;

UPDATE sentences_translations st, sentences s
SET st.translation_lang = s.lang
WHERE st.translation_id = s.id AND st.translation_lang IS NULL;

-- Update sentence_lang and translation_lang where lang is different than in sentences table.
UPDATE sentences_translations st, sentences s
SET st.sentence_lang = s.lang
WHERE st.sentence_id = s.id AND st.sentence_lang != s.lang;

UPDATE sentences_translations st, sentences s
SET st.translation_lang = s.lang
WHERE st.translation_id = s.id AND st.translation_lang != s.lang;

-- Delete links of deleted sentences
DELETE st FROM sentences_translations st
LEFT JOIN sentences s ON st.sentence_id = s.id
WHERE s.id IS NULL;

DELETE st FROM sentences_translations st
LEFT JOIN sentences s ON st.translation_id = s.id
WHERE s.id IS NULL;