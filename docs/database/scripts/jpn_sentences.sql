-- Used to convert into romaji
SELECT id, text FROM `sentences` WHERE lang = 'jpn'
INTO OUTFILE '/home/tatoeba/www/app/tmp/jpn_sentences.csv'
FIELDS TERMINATED BY ';' ENCLOSED BY '"' LINES TERMINATED BY '\n';