-- TODO for the moment the case sentences has an audio but no owner is not handle --
-- TODO for the moment we don't make the difference between shtooka / from user audio --
--      so a "user" audio can be kept even if a shtooka audio exist, but as no audio are removed --
--      we should be able to handle manually this --
Delimiter | 

DROP PROCEDURE IF EXISTS erase_and_relink_duplicate_sentence |

CREATE PROCEDURE erase_and_relink_duplicate_sentence(IN duplicate_text_id INT(11), IN duplicate_lang VARCHAR(4) )
BEGIN

  DECLARE duplicate_text VARBINARY(2500);

  DECLARE done INT DEFAULT 0;
  DECLARE temp_id INT;

  DECLARE curseur_ids CURSOR FOR
        select id  from sentences
        where text = (
            select text from sentences where id = duplicate_text_id limit 1
        ) AND lang = duplicate_lang ;

  DECLARE CONTINUE HANDLER FOR SQLSTATE '23000' SET duplicate_text_id = duplicate_text_id;
  DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;



  OPEN curseur_ids ;

  REPEAT
    FETCH curseur_ids INTO temp_id;
    IF done = 0 THEN
    
      select duplicate_text_id ;

        -- mettre à jour la relation   phrase/traduction --
      select 'update text -> translation' ;
      update sentences_translations
      set    sentence_id = duplicate_text_id
      where  sentence_id = temp_id;
 
      IF done = 1 THEN
        SET done = 0;
      END IF;

      select 'update translation -> text' ;
      update sentences_translations
      set    translation_id  = duplicate_text_id
      where  translation_id  = temp_id;

      IF done = 1 THEN
        SET done = 0;
      END IF;

        -- mettre à jour sentence annotation --
      select 'update text annotations' ;
      update sentence_annotations
      set    sentence_id  = duplicate_text_id
      where  sentence_id  = temp_id;

      IF done = 1 THEN
        SET done = 0;
      END IF;


    -- mettre à jour sentence_comments --
      select 'update text comments' ;
      update sentence_comments
      set    sentence_id  = duplicate_text_id
      where  sentence_id  = temp_id;

      IF done = 1 THEN
        SET done = 0;
      END IF;

    -- mettre à jour relation phrase -> liste --
     select 'update text lists' ;
      update sentences_sentences_lists
      set    sentence_id  = duplicate_text_id
      where  sentence_id  = temp_id;

      IF done = 1 THEN
        SET done = 0;
      END IF;

    -- mettre à jour relation phrase -> liste --
     select 'update tags' ;
      update tags_sentences
      set    sentence_id  = duplicate_text_id
      where  sentence_id  = temp_id;

      IF done = 1 THEN
        SET done = 0;
      END IF;


    -- mettre à jour sentence_annotations --
     select 'update sentence_annotations' ;
      update sentence_annotations 
      set    sentence_id  = duplicate_text_id
      where  sentence_id  = temp_id;

      IF done = 1 THEN
        SET done = 0;
      END IF;

      update sentence_annotations 
      set    meaning_id  = duplicate_text_id
      where  meaning_id  = temp_id;

      IF done = 1 THEN
        SET done = 0;
      END IF;
    -- delete duplicates --
    select 'erase duplicates' ; 
        -- strange stuff as mysql doesn't like when you delete using a direct subquery using the same
        -- table as the table that will have deleted lines
      delete from sentences where text = (
        select text from ( select text from sentences where id = duplicate_text_id ) as x
       ) and id != (duplicate_text_id) and lang = duplicate_lang;

      IF done = 1 THEN
        SET done = 0;
      END IF;

    END IF ;
  UNTIL done
  END REPEAT;
  CLOSE curseur_ids ;

END |

/******/


DROP PROCEDURE IF EXISTS erase_and_relink_all_duplicate_sentences |
CREATE PROCEDURE erase_and_relink_all_duplicate_sentences()
BEGIN

  DECLARE done INT DEFAULT 0;
  DECLARE duplicate_text VARBINARY(2500);
  DECLARE duplicate_text_id INT;
  DECLARE duplicate_lang VARCHAR(4);
  
  DECLARE curseur_text CURSOR FOR select text, lang from sentences group by text, lang having count(DISTINCT id ) > 1 ;

  DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
  
  OPEN curseur_text ;

  REPEAT
    FETCH curseur_text INTO duplicate_text,duplicate_lang ;
    IF done = 0 THEN
      select duplicate_text , duplicate_lang ;

      SELECT id INTO duplicate_text_id FROM sentences 
        WHERE text = duplicate_text AND lang = duplicate_lang
            AND user_id IS NOT NULL AND hasaudio != 'no' 
        ORDER BY created LIMIT 1 ;
      -- hack but no way, if a select ... into ...  return no result --
      -- it produce also the '02000' sql state (which is the same used --
      -- handle the end of cursor ...  --
      IF done = 1 THEN
        SET done = 0;

        -- si aucun duplicat n'a d'audio -
        SELECT id INTO duplicate_text_id FROM sentences 
          WHERE text = duplicate_text AND lang = duplicate_lang
            AND user_id IS NOT NULL  
          ORDER BY created LIMIT 1 ;
        IF done = 1 THEN
          SET done = 0;
          -- si tout les duplicats n'appartiennent à personne -
          SELECT id INTO duplicate_text_id FROM sentences 
            WHERE text = duplicate_text AND lang = duplicate_lang
            ORDER BY created LIMIT 1 ;

          SET done = 0;
        END IF;
      END IF;

      SELECT duplicate_text_id ;
      CALL erase_and_relink_duplicate_sentence(duplicate_text_id, duplicate_lang) ; --

    END IF ;
  UNTIL done
  END REPEAT;
  CLOSE curseur_text ;
  -- delete loop back --
  DELETE FROM sentences_translations WHERE sentence_id = translation_id ;
  -- reset sentence stats to comment if you don't have langStats table --
  START TRANSACTION ;
        TRUNCATE TABLE langStats;
        INSERT INTO langStats 
            SELECT lang , count(*) FROM sentences GROUP BY lang;
        CALL create_nbr_sentences_of_list();
  COMMIT ;
END |

Delimiter ;
