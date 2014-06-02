-- For all sentences with tag "old_tag_name", remove the old tag from the sentence
-- and add the tag "new_tag_name". At the conclusion of the script, delete the tag
-- "old_tag_name" from the tags table.
-- Use this script to retag sentences that were tagged with a misspelled version of an existing tag.
-- If there is no tag named old_tag_name or no tag named new_tag_name, the script will terminate.
-- Example: CALL retag('by Mark Twainx', 'by Mark Twain')
-- Script author: alanf
DELIMITER | 

DROP PROCEDURE IF EXISTS retag |

CREATE PROCEDURE retag(IN old_tag_name CHAR(70), IN new_tag_name CHAR(70))
ThisProc: BEGIN

DECLARE done INT DEFAULT 0;
DECLARE temp_sent_id INT(11);
DECLARE old_tag_id INT(11);
DECLARE new_tag_id INT(11);
DECLARE temp_user_id INT(11);
DECLARE old_nbr_of_sentences INT(11);
DECLARE new_nbr_of_sentences INT(11);
DECLARE temp_added_time DATETIME;

DECLARE sent_list CURSOR FOR
    SELECT sentence_id, user_id, added_time
    FROM `tags_sentences`
    WHERE `tags_sentences`.tag_id = old_tag_id;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

SELECT id INTO old_tag_id FROM tags
    WHERE name = old_tag_name;

SELECT id INTO new_tag_id FROM tags
    WHERE name = new_tag_name;

IF old_tag_id IS NULL THEN
    SELECT CONCAT('Tag ', '"', old_tag_name, '"', ' does not exist. Exiting.');
    LEAVE ThisProc;
ELSE
    SELECT CONCAT('Tag ', '"', old_tag_name, '"', ' has id ', old_tag_id, '.');
END IF;

IF new_tag_id IS NULL THEN
    SELECT CONCAT('Tag ', '"', new_tag_name, '"', ' does not exist. Exiting.');
    LEAVE ThisProc;
ELSE
    SELECT CONCAT('Tag ', '"', new_tag_name, '"', ' has id ', new_tag_id, '.');
END IF;

SELECT nbrOfSentences INTO old_nbr_of_sentences FROM tags
    WHERE id = old_tag_id;

SELECT nbrOfSentences INTO new_nbr_of_sentences FROM tags
    WHERE id = new_tag_id;

SELECT CONCAT('number of sentences for old tag: ', old_nbr_of_sentences);
SELECT CONCAT('number of sentences for new tag: ', new_nbr_of_sentences);

OPEN sent_list ;
REPEAT
    FETCH sent_list INTO
        temp_sent_id, temp_user_id, temp_added_time;

    IF NOT done THEN
        SELECT CONCAT('sentence: ', temp_sent_id, '; user: ', temp_user_id);
        INSERT INTO tags_sentences (tag_id, user_id, sentence_id, added_time)
               VALUES (new_tag_id, temp_user_id, temp_sent_id, temp_added_time);
        DELETE FROM tags_sentences WHERE tag_id = old_tag_id AND sentence_id = temp_sent_id;
        DELETE FROM tags WHERE id = old_tag_id; 
    END IF;
    UNTIL done
END REPEAT;

END |

DELIMITER ;