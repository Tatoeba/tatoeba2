DELIMITER | 

DROP PROCEDURE IF EXISTS revert_sentence_delete |

CREATE PROCEDURE revert_sentence_delete(IN deleted_sentence_id INT, IN user_who_deleted_id INT)

BEGIN

INSERT INTO sentences (id, text, user_id, created)
  SELECT c.sentence_id, c.text, c.user_id, c.datetime 
  FROM contributions AS c
  WHERE sentence_id = deleted_sentence_id 
    AND action = 'insert' 
    AND type = 'sentence';
INSERT INTO sentences_translations (sentence_id, translation_id, sentence_lang, translation_lang)
  SELECT c.sentence_id, c.translation_id, c.sentence_lang, c.translation_lang
  FROM contributions AS c
  WHERE sentence_id = deleted_sentence_id 
    AND action = 'insert' 
    AND type = 'link';
INSERT INTO sentences_translations (sentence_id, translation_id, sentence_lang, translation_lang)
  SELECT c.sentence_id, c.translation_id, c.sentence_lang, c.translation_lang
  FROM contributions AS c
  WHERE translation_id = deleted_sentence_id 
    AND action = 'insert' 
    AND type = 'link';
DELETE FROM contributions 
  WHERE sentence_id = deleted_sentence_id 
    AND user_id = user_who_deleted_id 
    AND action = 'delete';
DELETE FROM contributions 
  WHERE translation_id = deleted_sentence_id 
    AND user_id = user_who_deleted_id 
    AND action = 'delete';

END |

DELIMITER ;