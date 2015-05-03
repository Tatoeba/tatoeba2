ALTER TABLE `languages` ADD `audio` INT(10) NOT NULL DEFAULT 0;
ALTER TABLE `languages` ADD `group_1` TINYINT(2) NOT NULL DEFAULT 0;
ALTER TABLE `languages` ADD `group_2` SMALLINT(3) NOT NULL DEFAULT 0;
ALTER TABLE `languages` ADD `group_3` SMALLINT(4) NOT NULL DEFAULT 0;
ALTER TABLE `languages` ADD `group_4` INT(10) NOT NULL DEFAULT 0;
ALTER TABLE `languages` ADD `level_0` INT(10) NOT NULL DEFAULT 0;
ALTER TABLE `languages` ADD `level_1` INT(10) NOT NULL DEFAULT 0;
ALTER TABLE `languages` ADD `level_2` INT(10) NOT NULL DEFAULT 0;
ALTER TABLE `languages` ADD `level_3` INT(10) NOT NULL DEFAULT 0;
ALTER TABLE `languages` ADD `level_4` INT(10) NOT NULL DEFAULT 0;
ALTER TABLE `languages` ADD `level_5` INT(10) NOT NULL DEFAULT 0;
ALTER TABLE `languages` ADD `level_unknown` INT(10) NOT NULL DEFAULT 0;
ALTER TABLE `languages` CHANGE `numberOfSentences` `sentences` INT(10) unsigned NOT NULL DEFAULT '0';

-- Update number of audio
UPDATE `languages` l,
  (SELECT count(*) as count, lang
   FROM sentences
   WHERE hasaudio = 'shtooka'
   GROUP BY lang
  ) as s
SET audio = s.count
WHERE l.code = s.lang;

-- Update number of admins
UPDATE `languages` l, 
    (SELECT count(*) as count, language_code
        FROM users_languages JOIN users
        ON users.id = users_languages.of_user_id 
        WHERE users.group_id = 1
        GROUP BY users_languages.language_code
    ) as ul
    SET group_1 = ul.count
    WHERE l.code = ul.language_code;

-- Update number of corpus maintainers
UPDATE `languages` l, 
    (SELECT count(*) as count, language_code
        FROM users_languages JOIN users
        ON users.id = users_languages.of_user_id 
        WHERE users.group_id = 2
        GROUP BY users_languages.language_code
    ) as ul
    SET group_2 = ul.count
    WHERE l.code = ul.language_code;

-- Update number of advanced contributors
UPDATE `languages` l, 
    (SELECT count(*) as count, language_code
        FROM users_languages JOIN users
        ON users.id = users_languages.of_user_id 
        WHERE users.group_id = 3
        GROUP BY users_languages.language_code
    ) as ul
    SET group_3 = ul.count
    WHERE l.code = ul.language_code;

-- Update number of contributors
UPDATE `languages` l, 
    (SELECT count(*) as count, language_code
        FROM users_languages JOIN users
        ON users.id = users_languages.of_user_id 
        WHERE users.group_id = 4
        GROUP BY users_languages.language_code
    ) as ul
    SET group_4 = ul.count
    WHERE l.code = ul.language_code;

-- Update number of users with native level
UPDATE `languages` l,
  (SELECT count(*) as count, language_code
   FROM users_languages JOIN users
       ON users.id = users_languages.of_user_id
   WHERE users_languages.level = 5
   GROUP BY users_languages.language_code
  ) as ul
SET level_5 = ul.count
WHERE l.code = ul.language_code;

-- Update number of users with fluent level
UPDATE `languages` l,
  (SELECT count(*) as count, language_code
   FROM users_languages JOIN users
       ON users.id = users_languages.of_user_id
   WHERE users_languages.level = 4
   GROUP BY users_languages.language_code
  ) as ul
SET level_4 = ul.count
WHERE l.code = ul.language_code;

-- Update number of users with advanced level
UPDATE `languages` l,
  (SELECT count(*) as count, language_code
   FROM users_languages JOIN users
       ON users.id = users_languages.of_user_id
   WHERE users_languages.level = 3
   GROUP BY users_languages.language_code
  ) as ul
SET level_3 = ul.count
WHERE l.code = ul.language_code;

-- Update number of users with intermediate level
UPDATE `languages` l,
  (SELECT count(*) as count, language_code
   FROM users_languages JOIN users
       ON users.id = users_languages.of_user_id
   WHERE users_languages.level = 2
   GROUP BY users_languages.language_code
  ) as ul
SET level_2 = ul.count
WHERE l.code = ul.language_code;

-- Update number of users with beginner level
UPDATE `languages` l,
  (SELECT count(*) as count, language_code
   FROM users_languages JOIN users
       ON users.id = users_languages.of_user_id
   WHERE users_languages.level = 1
   GROUP BY users_languages.language_code
  ) as ul
SET level_1 = ul.count
WHERE l.code = ul.language_code;

-- Update number of users with no knowledge
UPDATE `languages` l,
  (SELECT count(*) as count, language_code
   FROM users_languages JOIN users
       ON users.id = users_languages.of_user_id
   WHERE users_languages.level = 0
   GROUP BY users_languages.language_code
  ) as ul
SET level_0 = ul.count
WHERE l.code = ul.language_code;

-- Update number of users with level unknown
UPDATE `languages` l,
  (SELECT count(*) as count, language_code
   FROM users_languages JOIN users
       ON users.id = users_languages.of_user_id
   WHERE users_languages.level IS NULL
   GROUP BY users_languages.language_code
  ) as ul
SET level_unknown = ul.count
WHERE l.code = ul.language_code;