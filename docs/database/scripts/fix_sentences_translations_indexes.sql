-- Add composite index on languages to speed up queries on language pairs
CREATE INDEX sentence_lang_translation_lang_idx
ON sentences_translations(sentence_lang, translation_lang);

-- Delete the single-column index on sentence_lang that has become useless
DROP INDEX sentence_lang 
ON sentences_translations;