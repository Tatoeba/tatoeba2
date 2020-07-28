-- create and fill last_contributions table
DROP TABLE IF EXISTS last_contributions;
CREATE TABLE last_contributions LIKE contributions ;
INSERT INTO last_contributions
  SELECT * FROM contributions
  WHERE type = 'sentence' # AND user_id != <bod_id>
  ORDER BY id DESC
  LIMIT 200 ;

-- create the trigger
DROP TRIGGER IF EXISTS insert_in_last_contributions ;
delimiter |
CREATE TRIGGER insert_in_last_contributions AFTER INSERT ON contributions
FOR EACH ROW BEGIN
  IF NEW.type = "sentence" THEN
    # IF NEW.type = "sentence" AND NEW.user_id != <bot_id> THEN

    INSERT INTO last_contributions (
      id,
      sentence_id,
      sentence_lang,
      translation_id,
      translation_lang,
      script,
      text,
      action,
      user_id,
      datetime,
      type
    )
    VALUES (
      NEW.id,
      NEW.sentence_id,
      NEW.sentence_lang,
      NEW.translation_id,
      NEW.translation_lang,
      NEW.script,
      NEW.text,
      NEW.action,
      NEW.user_id,
      NEW.datetime,
      NEW.type
    );
    -- delete the oldest contributions only if we have more than
    -- 200 contributions
    DELETE FROM last_contributions
    WHERE 200 < (
      SELECT count FROM (SELECT count(*) as count from last_contributions) AS t
    )
    ORDER BY id LIMIT 1;
  END IF;

  -- Update contributions_stats
  IF NEW.type != "license" AND NEW.action != "update" THEN
    -- If the deleted sentence was added today, decrease today's "insert" counter
    IF NEW.type = "sentence" AND NEW.action = "delete" THEN
      SET @addedTodayId = (
        SELECT id FROM contributions
        WHERE sentence_id = NEW.sentence_id AND type = "sentence" AND action = "insert" AND DATE(datetime) = CURDATE()
      );
      IF @addedTodayId IS NOT NULL THEN
        SET @toUpdateId = (
          SELECT id FROM contributions_stats
          WHERE `date` = CURDATE() AND `type` = NEW.type AND `action` = "insert"
        );
        UPDATE contributions_stats SET sentences = sentences - 1 WHERE id = @toUpdateId;
      END IF;
    END IF;
    IF @addedTodayId IS NULL THEN
      SET @statId = (
        SELECT id FROM contributions_stats
        WHERE `date` = CURDATE() AND `type` = NEW.type AND `action` = NEW.action
      );
      IF @statId IS NULL THEN
        INSERT INTO contributions_stats(`date`, `sentences`, `action`, `type`)
        VALUES (CURDATE(), 1, NEW.action, NEW.type);
      ELSE
        UPDATE contributions_stats SET sentences = sentences + 1 WHERE id = @statId;
      END IF;
    END IF;
  END IF;

END;
|
delimiter ;

-- create the trigger
DROP TRIGGER IF EXISTS update_lang_in_last_contributions ;
delimiter |
CREATE TRIGGER update_lang_in_last_contributions AFTER UPDATE ON contributions
FOR EACH ROW BEGIN
  IF NEW.type = "sentence" THEN

    -- The following code may cause problems in future if there are
    -- columns, in the last_contributions table, that would
    -- be expected to be updated.
    --
    -- The code below only updates the sentence_lang column,
    -- ignoring other columns. Worryingly, this behaviour may have to
    -- change according to future issues in the GitHub issue tracker.
    UPDATE last_contributions
      SET sentence_lang=NEW.sentence_lang
      WHERE sentence_id=OLD.sentence_id;

  END IF;

END;
|
delimiter ;
