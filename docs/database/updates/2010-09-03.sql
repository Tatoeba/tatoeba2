-- to centralize the "lang" string 
-- TODO : it's only a intermediate version to both not break backward compatibility 
-- and make the search engine optimisation / random optimisation  works
alter table langStats change lang lang varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
alter table langStats add column id tinyint unsigned not null auto_increment primary key ;

alter table sentences add column lang_id tinyint unsigned ;
update sentences , langStats set lang_id =  langStats.id  where langStats.lang = sentences.lang ;
