DROP TABLE IF EXISTS `wall2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wall2` (
  `id` int(11) NOT NULL auto_increment,
  `owner` int(11) NOT NULL,
  `parent_id` int(11) default NULL,
  `date` datetime NOT NULL,
  `title` varchar(255) character set utf8 NOT NULL,
  `content` BLOB NOT NULL,
  `lft` int(11) default NULL,
  `rght` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE wall_threads_last_message (
  `id` int(11) NOT NULL,
  `last_message_date`  datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

SET @id := 0;
SET @position := 0;
SET @@max_sp_recursion_depth = 1024 ;

DELIMITER |
DROP PROCEDURE create_new_wall |
CREATE PROCEDURE create_new_wall()
BEGIN

    DECLARE new_id INT(11);
    DECLARE new_lft INT(11);
    DECLARE new_rght INT(11);

    DECLARE done INT DEFAULT 0;
    DECLARE temp_id INT(11);
    DECLARE temp_owner INT(11);
    DECLARE temp_replyTo INT(11);
    DECLARE temp_content text charset utf8;
    DECLARE temp_title VARCHAR(255)  charset utf8;
    DECLARE temp_date datetime;

    DECLARE first_messages_list CURSOR FOR
        SELECT DISTINCT id , owner, replyTo, date, title, content
        FROM `wall`
        WHERE `wall`.replyTo = 0;
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN first_messages_list ;
    REPEAT

        FETCH first_messages_list INTO
            temp_id,
            temp_owner,
            temp_replyTo,
            temp_date,
            temp_title,
            temp_content;

        IF NOT done THEN



            SET @id := @id + 1;
            SET new_id := @id;
            
            SET @position := @position + 1;
            SET new_lft := @position;

            SELECT "call from first";
            CALL create_message( temp_id,new_id);

            SET @position := @position + 1;
            SET new_rght := @position;

            INSERT INTO wall2 (id, parent_id, owner, date, title, content, lft, rght)
            VALUES (new_id, null, temp_owner, temp_date, temp_title, temp_content, new_lft, new_rght);

            SELECT new_id,
                temp_id,
                0,
                new_lft,
                new_rght,
                'first';

        END IF ;

        UNTIL done
    END REPEAT;

    CLOSE first_messages_list;
END |

/********************************/

DROP PROCEDURE create_message |
CREATE PROCEDURE create_message(IN parent_id INT(11), IN new_parent_id INT(11))
BEGIN
    DECLARE done INT DEFAULT 0;

    DECLARE new_id INT(11);
    DECLARE new_lft INT(11);
    DECLARE new_rght INT(11);
    DECLARE temp_id INT(11);
    DECLARE temp_owner INT(11);
    DECLARE temp_replyTo INT(11);
    DECLARE temp_content text charset utf8;
    DECLARE temp_title VARCHAR(255)  charset utf8;
    DECLARE temp_date datetime;

    DECLARE all_messages_list CURSOR FOR
        SELECT DISTINCT id , owner, replyTo, date, title, content
        FROM `wall`
        WHERE `wall`.replyTo = parent_id ;
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;


    OPEN  all_messages_list;
    REPEAT
        FETCH all_messages_list INTO
            temp_id,
            temp_owner,
            temp_replyTo,
            temp_date,
            temp_title,
            temp_content;
        SELECT temp_id ;


        IF temp_id IS NOT null AND NOT done THEN

            SET @id := @id + 1;
            SET new_id := @id;
            
            SET @position := @position + 1;
            SET new_lft := @position;

            SELECT "call from recursiv";
            CALL create_message(temp_id,new_id);

            SET @position := @position + 1;
            SET new_rght := @position;


            INSERT INTO wall2 (id, parent_id, owner, date, title, content, lft, rght)
            VALUES  (new_id , new_parent_id , temp_owner, temp_date, temp_title, temp_content, new_lft, new_rght);

            SELECT new_id,
                temp_id,
                new_parent_id,
                new_lft,
                new_rght,
                'reply';

        END IF;

        UNTIL done
    END REPEAT;
    CLOSE all_messages_list;
END |
/***********************/

DROP PROCEDURE create_wall_threads_last_message |
CREATE PROCEDURE  create_wall_threads_last_message()
BEGIN
    DECLARE done INT DEFAULT 0;

    DECLARE tmp_id INT(11);
    DECLARE tmp_lft INT(11);
    DECLARE tmp_rght INT(11);

    DECLARE first_messages_list CURSOR FOR
        SELECT DISTINCT id , lft , rght
        FROM `wall`
        WHERE `wall`.parent_id is null;
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;


    OPEN first_messages_list ;
    REPEAT
        FETCH first_messages_list INTO
            tmp_id,
            tmp_lft,
            tmp_rght ;

        SELECT tmp_id;
        IF NOT done THEN
            INSERT INTO wall_threads_last_message (id, last_message_date)
            VALUES (tmp_id , (select date from wall where lft >= tmp_lft and rght <= tmp_rght order by date desc limit 1 ));

        END IF ;

        UNTIL done
    END REPEAT;

    CLOSE first_messages_list;
END |
/***********************/
DELIMITER ;

START TRANSACTION ;
    TRUNCATE TABLE wall2 ;
    TRUNCATE TABLE wall_threads_last_message;

    CALL create_new_wall() \G
    DELETE TABLE 'wall' ;
    RENAME TABLE 'wall2' to 'wall';
    CALL create_wall_threads_last_message ();

COMMIT ;
