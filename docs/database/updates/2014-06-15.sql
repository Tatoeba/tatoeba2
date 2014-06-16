-- From clean_ghost_links.sql:
-- Remove from the sentences_translations table any links between
-- sentences where at least one of the sentences is a "ghost" (does
-- not exist in the sentences table).
DELETE FROM sentences_translations WHERE sentence_id NOT IN (
    SELECT id FROM sentences) OR translation_id NOT IN (
    SELECT id FROM sentences);

-- From clean_sentences_unknown_lang.sql:
UPDATE sentences SET lang = NULL where lang_id = 1;
UPDATE sentences SET lang = NULL, lang_id = 1 where lang = "";
