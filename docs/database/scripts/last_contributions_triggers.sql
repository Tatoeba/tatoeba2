-- create and fill last_contributions table
CREATE TABLE last_contributions LIKE contributions ;
INSERT INTO last_contributions
    SELECT * FROM contributions ORDER BY id DESC LIMIT 200 ;

-- create the trigger
DROP TRIGGER insert_in_last_contributions ;
delimiter |
CREATE TRIGGER insert_in_last_contributions AFTER INSERT ON contributions
  FOR EACH ROW BEGIN
    IF NEW.type = "sentence" THEN


        INSERT INTO last_contributions (
            id,
            sentence_id,
            sentence_lang,
            translation_id,
            translation_lang,
            text,
            action,
            user_id,
            datetime,
            ip,
            type
        )
        VALUES (
            NEW.id,
            NEW.sentence_id,
            NEW.sentence_lang,
            NEW.translation_id,
            NEW.translation_lang,
            NEW.text,
            NEW.action,
            NEW.user_id,
            NEW.datetime,
            NEW.ip,
            NEW.type
        );
        -- delete the oldest contributions only if we have more than
        -- 200 contributions
        DELETE FROM last_contributions 
            WHERE 200 < (
                SELECT count FROM (
                    SELECT count(*) as count from last_contributions
                ) AS t
            )
            ORDER BY id LIMIT 1;

    END IF;
  END;
|
delimiter ;

