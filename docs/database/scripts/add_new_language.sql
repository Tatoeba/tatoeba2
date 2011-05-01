-- Procedure to add a new language in the database.
-- 1) Add it in langStats.
-- 2) Update the 'lang' field of the sentences involved.
-- 3) Update the count in the langStats.
--
-- Example:
-- CALL add_new_language('gla', 412, 'Scottish Gaelic');
--
-- If there is only a list:
-- CALL add_new_language('gla', 412, null);
--
-- If there are only tags:
-- CALL add_new_language('gla', 0, 'Scottish Gaelic');

DELIMITER | 

DROP PROCEDURE IF EXISTS add_new_language |

CREATE PROCEDURE add_new_language(IN lang_iso_code CHAR(4), IN list_id_for_lang INT, IN tag_name_for_lang CHAR(50))
BEGIN

INSERT INTO langStats (lang) VALUES (lang_iso_code); 
UPDATE sentences, sentences_sentences_lists 
    SET lang = lang_iso_code, lang_id = LAST_INSERT_ID()
    WHERE sentences_list_id = list_id_for_lang AND id = sentence_id;
UPDATE sentences, tags_sentences 
    SET lang = lang_iso_code, lang_id = LAST_INSERT_ID() 
    WHERE tag_id = (SELECT id FROM tags WHERE name = tag_name_for_lang) AND id = sentence_id;
UPDATE langStats 
    SET numberOfSentences = (SELECT count(*) FROM sentences WHERE lang = lang_iso_code) 
    WHERE lang = lang_iso_code;

END |

DELIMITER ;