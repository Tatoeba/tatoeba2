<?php

$languages = array(
    'ara' ,
    'eng' ,
    'jpn' ,
    'fra' ,
    'deu' ,
    'spa' ,
    'ita' ,
    'vie' ,
    'rus' ,
    'cmn' ,
    'kor' ,
    'nld' ,
    'heb' ,
    'ind' ,
    'por' ,
    'fin' ,
    'bul' ,
    'ukr' ,
    'ces' ,
    'epo' ,
    'ell' ,
    'tur' ,
    'swe' ,
    'nob' ,
    'zsm' ,
    'est' ,
    'kat' ,
    'pol' ,
    'swh' ,
    'lat' ,
    'wuu' ,
    'arz' ,
    'bel' ,
    'hun' ,
    'isl' ,
    'sqi' ,
    'yue' ,
    'afr' ,
    'fao' ,
    'fry' ,
    'bre' ,
    'ron' ,
    'uig' ,
    'uzb' ,
    'non',
    'srp',
    'tat',
    'yid',
    'pes',
    'nan',
    'eus',
    'slk',
    'dan',
    'hye',
    'acm',
    'san',
    'urd',
    'hin',
    'ben',
    'cycl',
    'cat',
    'kaz',
    'lvs',
);

// create table for each 
foreach($languages as $language) {
    echo "CREATE TABLE IF NOT EXISTS random_sentence_id_$language (
  `id` INT NOT NULL auto_increment,
  `sentence_id` INT NOT NULL,
  PRIMARY KEY  (`id`),
  KEY(sentence_id)
 ) ENGINE=MyISAM;\n\n";
 
}


foreach($languages as $language) {
    echo"
    SET @id = 0;
    TRUNCATE random_sentence_id_$language;
    INSERT INTO random_sentence_id_$language SELECT @id := @id + 1, id FROM sentences WHERE lang = '$language';
    
    ";
}
// create trigger on INSERT
echo"
DELIMITER |
DROP TRIGGER IF EXISTS insert_in_random|
CREATE TRIGGER insert_in_random
AFTER INSERT ON sentences FOR EACH ROW
BEGIN
    DECLARE m BIGINT UNSIGNED DEFAULT 1;
    IF NEW.lang IS NULL THEN
        SELECT 1 INTO m;
   
";
foreach($languages as $i=>$language) {
    echo"
    ELSEIF NEW.lang = '$language' THEN
    

        SELECT MAX(id) + 1 FROM random_sentence_id_$language INTO m;
        SELECT IFNULL(m, 1) INTO m;

        INSERT INTO random_sentence_id_$language VALUES (m, NEW.id);
    ";
}

echo
"
    END IF;
END|
DELIMITER ;
";

// create trigger on DELETE
echo"
DELIMITER |
DROP TRIGGER IF EXISTS delete_dependencies_of_sentences ;
DROP TRIGGER IF EXISTS delete_sentence|
CREATE TRIGGER  delete_sentence
AFTER DELETE ON sentences FOR EACH ROW
BEGIN
   DECLARE m BIGINT UNSIGNED DEFAULT 1;
   IF OLD.lang IS NULL THEN
        SELECT 1 INTO m;
   
";
foreach($languages as $i=>$language) {
    echo"
    ELSEIF OLD.lang = '$language' THEN
    
        SELECT id FROM random_sentence_id_$language WHERE sentence_id = OLD.id INTO m;
        SELECT IFNULL(m, 1) INTO m;
        DELETE FROM random_sentence_id_$language WHERE sentence_id = OLD.id;
        UPDATE random_sentence_id_$language SET id = m WHERE id = (SELECT MAX(id) FROM rand_sentence_id_$language);
    ";
}

echo
"    
    -- decreament the number of sentence for all list
    -- which contain the sentence to delete
    UPDATE `sentences_lists`
    SET `numberOfSentences` = `numberOfSentences` - 1
    WHERE id IN 
    (
      SELECT `sentences_list_id` FROM `sentences_sentences_lists`
      WHERE `sentence_id` = OLD.`id`
    );
    
    -- delete the sentence of the list
    DELETE FROM `sentences_sentences_lists`
    WHERE `sentence_id` = OLD.`id`;
    
    -- delete associated to the sentences
    DELETE FROM `tags_sentences`
    WHERE `sentence_id` = OLD.`id`;
    END IF;

END|
DELIMITER ;
";
// create trigger on UPDATE
echo"
DELIMITER |
DROP TRIGGER IF EXISTS update_in_random|
CREATE TRIGGER update_in_random
AFTER UPDATE ON sentences FOR EACH ROW
BEGIN
    DECLARE m BIGINT UNSIGNED DEFAULT 1;
    IF NEW.lang != OLD.lang THEN
    IF NEW.lang IS NULL THEN
        SELECT 1 INTO m;
   
";
foreach($languages as $language) {
    echo"
    ELSEIF NEW.lang = '$language' THEN
    
        SELECT MAX(id) + 1 FROM random_sentence_id_$language INTO m;
        SELECT IFNULL(m, 1) INTO m;
        INSERT INTO random_sentence_id_$language VALUES (m, NEW.id);
        IF NEW.lang IS NULL THEN
            SELECT 1 INTO m;
    ";

    foreach($languages as $oldLanguage) {
        echo "
        ELSEIF OLD.lang = '$oldLanguage' THEN
            SELECT id FROM random_sentence_id_$oldLanguage WHERE sentence_id = OLD.id INTO m;
            SELECT IFNULL(m, 1) INTO m;
            DELETE FROM random_sentence_id_$oldLanguage WHERE sentence_id = OLD.id;
            UPDATE random_sentence_id_$oldLanguage SET id = m WHERE id = (SELECT MAX(id) FROM rand_sentence_id_$language);
        ";
    }

    echo "
        END IF; 
    ";
}

echo
"
    END IF;
    END IF;
END|
DELIMITER ;
";


