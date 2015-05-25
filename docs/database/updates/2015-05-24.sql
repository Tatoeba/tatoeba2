ALTER TABLE users ADD settings BLOB NOT NULL AFTER `description`;

UPDATE users SET settings = CONCAT('{',
    '"is_public":',          IF(is_public,'1','0'),',',
    '"lang":',               IF(lang is null,'null', CONCAT('"',lang,'"')),
'}');

ALTER TABLE users DROP is_public;
ALTER TABLE users DROP lang;

