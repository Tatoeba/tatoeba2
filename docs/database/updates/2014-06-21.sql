-- Remove leading and trailing whitespace from tag names
UPDATE tags SET `name` = TRIM(`name`) WHERE `name` LIKE ' %' OR `name` LIKE '% ';
