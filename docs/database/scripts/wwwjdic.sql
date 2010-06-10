SELECT sa.sentence_id, sa.meaning_id, s.text, t.text, sa.text
FROM sentence_annotations sa 
  JOIN sentences s ON s.id = sa.sentence_id
  JOIN sentences t ON t.id = sa.meaning_id
    WHERE sa.meaning_id != -1
INTO OUTFILE '/var/tmp/wwwjdic.csv';