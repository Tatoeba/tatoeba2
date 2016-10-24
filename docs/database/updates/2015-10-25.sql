ALTER TABLE `vocabulary` ADD `hash` BINARY(16) NOT NULL;
UPDATE `vocabulary` SET `hash` = `id`;
ALTER TABLE `vocabulary` DROP PRIMARY KEY;
UPDATE vocabulary SET id = 0;
ALTER TABLE `vocabulary` MODIFY `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT;
