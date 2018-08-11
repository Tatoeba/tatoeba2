-- Make sure that nbrOfSentences is correct for each tag by checking count(*)
-- in tags_sentences. 
-- These got out of sync in the past because each delete of a sentence was 
-- double-decrementing the sentence count for each tag associated with that sentence. 
-- If other bugs come up that cause the sentence count to get out of sync, fix them, 
-- then run this script.
-- Syntax: CALL clean_up_tag_sentence_count()
-- Script author: alanf

DELIMITER | 

DROP PROCEDURE IF EXISTS clean_up_tag_sentence_count |

CREATE PROCEDURE clean_up_tag_sentence_count()
ThisProc: BEGIN

DECLARE done INT DEFAULT 0;
DECLARE temp_id INT(11);
DECLARE temp_name VARCHAR(50);
DECLARE temp_nbr_of_sentences INT(11);
DECLARE new_nbr_of_sentences INT(11);

DECLARE tag_list CURSOR FOR
    SELECT id, name, nbrOfSentences
    FROM tags;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

OPEN tag_list ;
REPEAT
    FETCH tag_list INTO
        temp_id, temp_name, temp_nbr_of_sentences;

    IF NOT done THEN
        SELECT COUNT(DISTINCT sentence_id) INTO new_nbr_of_sentences
        FROM tags_sentences
        WHERE tag_id = temp_id;
        -- Tags whose names begin with "@" are "attention" tags and should not be
        -- deleted automatically when they are no longer attached to any sentences.
        -- The idea is that we might temporarily bring the number of sentences marked,
        -- for example, '@check' down to zero, but it's still a useful tag that we will
        -- want to use in the future. Such tags can always be deleted via manually executed
        -- SQL statements. 
        IF new_nbr_of_sentences = 0 AND NOT temp_name LIKE '@%' THEN
            SELECT CONCAT('Deleting ', '"', temp_name, '"');
            DELETE FROM tags where id = temp_id;
        ELSE
            IF new_nbr_of_sentences <> temp_nbr_of_sentences THEN
                SELECT CONCAT('CHANGED: name: ', '"', temp_name, '" from ', temp_nbr_of_sentences,
                   ' to ', new_nbr_of_sentences);
                UPDATE tags
                   SET nbrOfSentences = new_nbr_of_sentences
                WHERE id = temp_id;
            END IF;
        END IF;
    END IF;
    UNTIL done
END REPEAT;

END |

DELIMITER ;