DELIMITER |
DROP TRIGGER IF EXISTS delete_dependencies_of_sentences ;
DROP TRIGGER IF EXISTS delete_sentence|
CREATE TRIGGER  delete_sentence
AFTER DELETE ON sentences FOR EACH ROW
BEGIN

    -- decreament the number of sentence for all list
    -- which contain the sentence to delete
    UPDATE `sentences_lists`
    SET `numberOfSentences` = `numberOfSentences` - 1
    WHERE id IN 
    (
      SELECT `sentences_list_id` FROM `sentences_sentences_lists`
      WHERE `sentence_id` = OLD.`id`
    );
    
    -- delete the sentence of the list
    DELETE FROM `sentences_sentences_lists`
    WHERE `sentence_id` = OLD.`id`;
    
    -- delete associated to the sentences
    DELETE FROM `tags_sentences`
    WHERE `sentence_id` = OLD.`id`;
    END IF;

END|
DELIMITER ;
