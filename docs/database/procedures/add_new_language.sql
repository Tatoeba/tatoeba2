-- Procedure to add a new language in the database.
-- 1) Add it in 'languages' table.
-- 2) Update the 'lang' field of the sentences involved.
-- 3) Update the count in the 'languages' table.
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
ThisProc: BEGIN

SELECT COUNT(*) INTO @sentences_in_list FROM sentences, sentences_sentences_lists 
    WHERE sentences_list_id = list_id_for_lang AND id = sentence_id; 
SELECT COUNT(*) INTO @sentences_in_tags FROM sentences, tags_sentences 
    WHERE tag_id = (SELECT id FROM tags WHERE name = tag_name_for_lang) AND id = sentence_id;
IF (@sentences_in_list = 0 AND @sentences_in_tags = 0) THEN
    select CONCAT('There are no sentences in list ', list_id_for_lang, 
              ', and no sentences with tag ', tag_name_for_lang, '.');
    LEAVE ThisProc;
END IF;

INSERT INTO languages (code) VALUES (lang_iso_code);
UPDATE sentences, sentences_sentences_lists 
    SET lang = lang_iso_code, lang_id = LAST_INSERT_ID()
    WHERE sentences_list_id = list_id_for_lang AND id = sentence_id;
UPDATE sentences, tags_sentences 
    SET lang = lang_iso_code, lang_id = LAST_INSERT_ID() 
    WHERE tag_id = (SELECT id FROM tags WHERE name = tag_name_for_lang) AND id = sentence_id;
UPDATE languages
    SET numberOfSentences = (SELECT count(*) FROM sentences WHERE lang = lang_iso_code) 
    WHERE code = lang_iso_code;

END |

DELIMITER ;