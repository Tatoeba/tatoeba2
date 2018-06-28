-- create the trigger

-- on insertion
DROP TRIGGER IF EXISTS extend_sentences_lists_add ;
delimiter |

CREATE TRIGGER extend_sentences_lists_add AFTER INSERT ON `sentences_sentences_lists`
FOR EACH ROW BEGIN
      UPDATE `sentences_lists` AS sl
      SET sl.modified = NOW()
      WHERE sl.id = NEW.sentences_list_id
      LIMIT 1;
END;


-- delete
DROP TRIGGER IF EXISTS extend_sentences_lists_delete |
CREATE TRIGGER extend_sentences_lists_delete AFTER DELETE ON `sentences_sentences_lists`
FOR EACH ROW BEGIN
      UPDATE `sentences_lists` AS sl
      SET sl.modified = NOW()
      WHERE sl.id = OLD.sentences_list_id
      LIMIT 1;
END;

|


delimiter ;
