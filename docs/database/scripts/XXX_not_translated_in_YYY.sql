-- Sentences in language XXX that do not have translation in language YYY.

SELECT s.id FROM sentences s 
  JOIN sentences_translations st ON ( s.id = st.sentence_id ) 
  JOIN sentences t on ( st.translation_id = t.id ) 
WHERE s.lang = 'XXX' 
  AND s.id NOT IN 
  ( 
    SELECT DISTINCT s.id FROM sentences s 
      JOIN sentences_translations st ON ( s.id = st.sentence_id ) 
      JOIN sentences t on ( st.translation_id = t.id ) 
    WHERE t.lang = "YYY" AND s.lang = "XXX"
  ) 
LIMIT 200;