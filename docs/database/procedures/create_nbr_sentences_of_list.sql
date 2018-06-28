
Delimiter |
DROP PROCEDURE IF EXISTS create_nbr_sentences_of_list |
CREATE PROCEDURE create_nbr_sentences_of_list()
BEGIN

    DECLARE done INT DEFAULT 0;
    DECLARE list_id INT(11);
    DECLARE nbr_sentences MEDIUMINT(8);
    
    DECLARE curseur_list CURSOR FOR
        SELECT `sentences_lists`.`id`
        FROM `sentences_lists`;
    
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN curseur_list ;
    

    
    REPEAT
        FETCH curseur_list INTO list_id ;
        
        SELECT COUNT(`sentences_sentences_lists`.`sentences_list_id`) INTO nbr_sentences
            FROM `sentences_lists`
            LEFT JOIN `sentences_sentences_lists`
            ON `sentences_lists`.`id` = `sentences_sentences_lists`.`sentences_list_id`
            WHERE `sentences_lists`.`id` = list_id
            GROUP BY `sentences_lists`.`id`;
        
        UPDATE `sentences_lists` SET `sentences_lists`.`numberOfSentences` = nbr_sentences
            WHERE `sentences_lists`.`id` = list_id;
    
    UNTIL done    
    END REPEAT;

END |
Delimiter ;
