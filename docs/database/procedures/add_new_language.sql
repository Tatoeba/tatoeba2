-- Procedure to add a new language in the database.
-- 1) Add it in 'languages' table.
-- 2) Update the 'lang' field of the sentences involved.
-- 3) Update the count in the 'languages' table.
--
-- Example:
-- CALL add_new_language('gla', 412);
--
-- If there is no list:
-- CALL add_new_language('gla', 0);

DELIMITER |

DROP PROCEDURE IF EXISTS add_new_language |

CREATE PROCEDURE add_new_language(IN lang_iso_code CHAR(4), IN list_id_for_lang INT)
ThisProc: BEGIN

-- We know this will fail when the language already exists, but we want the exception
-- to occur so the caller will catch it.
INSERT INTO languages (code) VALUES (lang_iso_code);

IF (list_id_for_lang > 0) THEN
    SELECT COUNT(*) INTO @sentences_in_list FROM sentences, sentences_sentences_lists
        WHERE sentences_list_id = list_id_for_lang AND sentences.id = sentence_id;

    IF (@sentences_in_list = 0) THEN
        select CONCAT('There are no sentences in list ', list_id_for_lang) AS Warning;
        LEAVE ThisProc;
    END IF;

    UPDATE sentences, sentences_sentences_lists
        SET lang = lang_iso_code
        WHERE sentences_list_id = list_id_for_lang
        AND sentences.id = sentence_id;
    UPDATE languages
        SET sentences = (SELECT count(*) FROM sentences WHERE lang = lang_iso_code)
        WHERE code = lang_iso_code;
END IF;

END |

DELIMITER ;
