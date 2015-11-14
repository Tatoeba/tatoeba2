ALTER TABLE `languages` MODIFY `id` INT(10) unsigned NOT NULL AUTO_INCREMENT;

UPDATE `languages` SET `code` = 'nno' WHERE `code` = 'non';
UPDATE `sentences` SET `lang` = 'nno' WHERE `lang` = 'non';