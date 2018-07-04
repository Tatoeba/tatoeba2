
Delimiter |
DROP PROCEDURE IF EXISTS create_nbr_sentences_of_tag |
CREATE PROCEDURE create_nbr_sentences_of_tag()
BEGIN

    DECLARE done INT DEFAULT 0;
    DECLARE tagId INT(11);
    DECLARE nbr_sentences MEDIUMINT(8);
    
    DECLARE tags_cursor CURSOR FOR
        SELECT id
        FROM tags;
    
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN tags_cursor ;
    

    
    REPEAT
        FETCH tags_cursor INTO tagId ;
        
        SELECT COUNT(`tags_sentences`.`tag_id`) INTO nbr_sentences
            FROM `tags`
            LEFT JOIN `tags_sentences`
            ON `tags`.`id` = `tags_sentences`.`tag_id`
            WHERE `tags`.`id` = tagId
            GROUP BY `tags`.`id`;
        
        UPDATE `tags` SET nbrOfSentences = nbr_sentences
            WHERE id = tagId;
    
    UNTIL done    
    END REPEAT;

END |
Delimiter ;
