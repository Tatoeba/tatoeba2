ALTER TABLE tags MODIFY COLUMN internal_name VARCHAR(50) DEFAULT NULL;



--
-- docs/database/scripts/clean_up_tag_sentence_count.sql
--

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
        SELECT COUNT(*) INTO new_nbr_of_sentences FROM tags_sentences WHERE tag_id = temp_id;
        IF new_nbr_of_sentences = 0 THEN
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



--
-- docs/database/scripts/delete_dependencies_of_sentences.sql
--

DROP TRIGGER IF EXISTS decrease_number_of_sentence_of_list ;
DROP TRIGGER IF EXISTS delete_dependencies_of_sentences ;
Delimiter |
CREATE TRIGGER delete_dependencies_of_sentences AFTER DELETE ON sentences
  FOR EACH ROW BEGIN
    
    -- Decrement the sentence count for all lists
    -- that contain the sentence to delete.
    UPDATE `sentences_lists`
    SET `numberOfSentences` = `numberOfSentences` - 1
    WHERE id IN 
    (
      SELECT `sentences_list_id` FROM `sentences_sentences_lists`
      WHERE `sentence_id` = OLD.`id`
    );
    
    -- Delete the sentence from the list.
    DELETE FROM `sentences_sentences_lists`
    WHERE `sentence_id` = OLD.`id`;

    -- This will also invoke a trigger in
    -- maintain_tags_number_of_sentences.sql.
    DELETE FROM `tags_sentences`
    WHERE `sentence_id` = OLD.`id`;
    
  END|
Delimiter ;



--
-- docs/database/scripts/maintain_tags_number_of_sentences.sql
--

-- create the trigger
-- on insertion
DROP TRIGGER insert_in_tags ;
delimiter |
CREATE TRIGGER insert_in_tags AFTER INSERT ON tags_sentences
    FOR EACH ROW BEGIN
        UPDATE tags SET nbrOfSentences = nbrOfSentences + 1 WHERE id = NEW.tag_id;
    END;
|
-- delete
DROP TRIGGER remove_tag_from_sentence |
CREATE TRIGGER remove_tag_from_sentence AFTER DELETE ON tags_sentences
    FOR EACH ROW BEGIN
        DELETE FROM tags WHERE id = OLD.tag_id AND nbrOfSentences = 1;
        UPDATE tags SET nbrOfSentences = nbrOfSentences - 1 WHERE id = OLD.tag_id;
    END;
|
-- update 
DROP TRIGGER update_tags_sentences |
CREATE TRIGGER update_tags_sentences AFTER UPDATE ON tags_sentences
    FOR EACH ROW BEGIN
        UPDATE tags SET nbrOfSentences = nbrOfSentences - 1 WHERE id = OLD.tag_id;
        UPDATE tags SET nbrOfSentences = nbrOfSentences + 1 WHERE id = NEW.tag_id;
    END;
|
delimiter ;