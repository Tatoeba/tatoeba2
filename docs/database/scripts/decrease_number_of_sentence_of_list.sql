DROP TRIGGER decrease_number_of_sentence_of_list ;
Delimiter |
CREATE TRIGGER decrease_number_of_sentence_of_list AFTER DELETE ON sentences
  FOR EACH ROW BEGIN
    
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
    
  END|
Delimiter ;