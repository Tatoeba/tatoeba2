-- TODO to delete this is now handle in the generate_rand_table.php
-- if decrease_number_of_sentence_of_list exist kill them (it's the ancestor)
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
