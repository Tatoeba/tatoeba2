
-- otherwise i will forget this tricky request
 update languages,  (select count(*) as nbr , lang  from sentences group by lang ) as s  set sentences = nbr where languages.code =s.lang;
