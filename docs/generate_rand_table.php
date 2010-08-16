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
  `id` int() NOT NULL auto_increment,
  `sentence_id` int() NOT NULL,
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

echo"
DELIMITER |
DROP TRIGGER IF EXISTS insert_in_random|
CREATE TRIGGER insert_in_random
AFTER INSERT ON sentences FOR EACH ROW
BEGIN
   DECLARE m BIGINT UNSIGNED DEFAULT 1;

   IF LANGUAGE
";
foreach($languages as $language) {
    echo"
    SELECT MAX(id) + 1 FROM random_sentence_id_$language INTO m;
    SELECT IFNULL(m, 1) INTO m;

    INSERT INTO random_sentence_id_$language VALUES (m, NEW.id);
"
}

echo
"
END|
DELIMITER ;
";

