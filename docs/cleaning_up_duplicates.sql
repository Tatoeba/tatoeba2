DELIMITER |  

/*DROP PROCEDURE erase_and_relink_all_duplicate_sentences |
DROP PROCEDURE erase_and_relink_duplicate_sentence |*/
CREATE PROCEDURE erase_and_relink_duplicate_sentence(IN duplicate_text VARCHAR(500))

BEGIN

DECLARE duplicate_text_id INT;
select id into duplicate_text_id  from sentences where text = duplicate_text limit 1 ;
select duplicate_text_id ,duplicate_text ;

/*mettre à jour la relation   phrase->traduction*/
select "update text -> translation" ;
update sentences_translations
set    sentence_id = duplicate_text_id
where  sentences_translations.sentence_id  in
(select id  from sentences where text = duplicate_text);

select "update translation -> text" ;
update sentences_translations
set    translation_id  = duplicate_text_id
where  sentences_translations.translation_id  in
( select id  from sentences where text = duplicate_text);

/*mettre à jour sentence annotation*/
select "update text annotations" ;
update sentence_annotations
set    sentence_id  = duplicate_text_id
where  sentence_annotations.sentence_id  in
(select id  from sentences where text = duplicate_text);

/*mettre à jour sentence_comments*/
select "update text comments" ;
update sentence_comments
set    sentence_id  = duplicate_text_id
where  sentence_comments.sentence_id  in
(select id from sentences where text = duplicate_text);

/*mettre à jour relation phrase -> liste*/
select "update text lists" ;
update sentences_sentences_lists
set    sentence_id  = duplicate_text_id
where  sentences_sentences_lists.sentence_id  in
(select id  from sentences where text = duplicate_text);

/*delete duplicates*/
select "erase duplicates" ;
delete from sentences where text = duplicate_text and id not in (duplicate_text_id);
END |

CREATE PROCEDURE erase_and_relink_all_duplicate_sentences()
BEGIN
DECLARE done INT DEFAULT 0;
DECLARE duplicate_text VARCHAR(500) ;


DECLARE curseur_text CURSOR FOR select text  from sentences group by text having count(DISTINCT id ) > 1 ;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
DECLARE CONTINUE HANDLER FOR SQLSTATE '23000' SET done = done;
OPEN curseur_text ;

REPEAT
FETCH curseur_text INTO duplicate_text ;
IF done = 0 THEN
CALL erase_and_relink_duplicate_sentence(duplicate_text) ;
END IF ;
UNTIL done
END REPEAT;
END |