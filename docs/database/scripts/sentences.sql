SELECT id, lang, text FROM `sentences`
INTO OUTFILE '/home/tatoeba/www/app/tmp/sentences.csv'
FIELDS TERMINATED BY ';' ENCLOSED BY '"' LINES TERMINATED BY '\n';