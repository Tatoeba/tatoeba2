
-- otherwise i will forget this tricky request
 update langStats,  (select count(*) as nbr , lang  from sentences group by lang ) as s  set numberOfSentences = nbr where langStats.lang =s.lang;
