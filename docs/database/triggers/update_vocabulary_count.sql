DROP TRIGGER IF EXISTS increment_vocabulary_count;
DROP TRIGGER IF EXISTS decrement_vocabulary_count;

delimiter |

CREATE TRIGGER increment_vocabulary_count AFTER INSERT ON users_vocabulary
FOR EACH ROW BEGIN
    UPDATE vocabulary SET numAdded = numAdded + 1 WHERE id = NEW.vocabulary_id;
END;

CREATE TRIGGER decrement_vocabulary_count AFTER DELETE ON users_vocabulary
FOR EACH ROW BEGIN
    UPDATE vocabulary SET numAdded = numAdded - 1 WHERE id = OLD.vocabulary_id;
END;

|

delimiter ;