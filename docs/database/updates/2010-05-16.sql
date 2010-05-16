-- will set default value for country_id to NULL and remove the old odd value -- 
ALTER TABLE users CHANGE country_id country_id varchar(2) default null;
UPDATE users SET country_id = NULL WHERE country_id = "0" OR country_id = "";
