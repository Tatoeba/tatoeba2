DELIMITER | 

DROP PROCEDURE IF EXISTS retag |

CREATE PROCEDURE retag(IN old_tag_name CHAR(50), IN new_tag_name CHAR(50))
ThisProc: BEGIN

DECLARE done INT DEFAULT 0;
DECLARE temp_sent_id INT(11);

DECLARE sent_list CURSOR FOR
    SELECT sentence_id
    FROM `tags_sentences`
    WHERE `tags_sentences`.tag_id = 4585;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

SELECT id INTO @old_tag_id FROM tags
    WHERE name = old_tag_name;

SELECT id INTO @new_tag_id FROM tags
    WHERE name = new_tag_name;

IF @old_tag_id IS NULL THEN
    SELECT CONCAT('Tag ', old_tag_name, ' does not exist. Exiting.');
    LEAVE ThisProc;
ELSE
    SELECT CONCAT('Tag ', old_tag_name, ' has id ', @old_tag_id, '.');
END IF;

IF @new_tag_id IS NULL THEN
    SELECT CONCAT('Tag ', new_tag_name, ' does not exist. Exiting.');
    LEAVE ThisProc;
ELSE
    SELECT CONCAT('Tag ', new_tag_name, ' has id ', @new_tag_id, '.');
END IF;

SELECT nbrOfSentences INTO @old_nbr_of_sentences FROM tags
    WHERE id = @old_tag_id;

SELECT nbrOfSentences INTO @new_nbr_of_sentences FROM tags
    WHERE id = @new_tag_id;

SELECT CONCAT('number of sentences for old tag: ', @old_nbr_of_sentences);
SELECT CONCAT('number of sentences for new tag: ', @new_nbr_of_sentences);

OPEN sent_list ;
REPEAT
    FETCH sent_list INTO
        temp_sent_id;

    IF NOT done THEN
        SELECT CONCAT('sentence: ', temp_sent_id);
    END IF;
    UNTIL done
END REPEAT;


END |

DELIMITER ;