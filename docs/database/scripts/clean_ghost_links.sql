-- Remove from the sentences_translations table any links between
-- sentences where at least one of the sentences is a "ghost" (does
-- not exist in the sentences table).
DELETE FROM sentences_translations WHERE sentence_id NOT IN (
    SELECT id FROM sentences) OR translation_id NOT IN (
    SELECT id FROM sentences);