SELECT st.sentence_id, st.translation_id, s.text, t.text, sa.text
FROM sentences s 
	LEFT JOIN sentence_annotations sa ON s.id = sa.sentence_id
	LEFT JOIN sentences_translations st ON st.sentence_id = s.id
	LEFT JOIN sentences t ON st.translation_id = t.id
WHERE s.lang = 'jpn' 
    AND (t.lang = 'eng' OR t.text IS NULL)
    AND sa.text IS NOT NULL
INTO OUTFILE '/home/tatoeba/www/app/tmp/wwwjdic.csv'
FIELDS TERMINATED BY ';' ENCLOSED BY '"' LINES TERMINATED BY '\n';