# Move current id to hash column
ALTER TABLE `vocabulary` ADD hash BINARY(16) NOT NULL;
UPDATE `vocabulary` SET hash = id;

# Change id to auto-increment int
ALTER TABLE `vocabulary` DROP PRIMARY KEY;
UPDATE vocabulary SET id = 0;
ALTER TABLE `vocabulary` MODIFY id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT;

# Update users_vocabulary vocabulary_id column
ALTER TABLE `users_vocabulary` CHANGE vocabulary_id hash binary(16) NOT NULL;
ALTER TABLE `users_vocabulary` ADD vocabulary_id int(11) NOT NULL;
UPDATE `users_vocabulary`
    LEFT OUTER JOIN `vocabulary` ON(vocabulary.hash=users_vocabulary.hash)
    SET vocabulary_id = vocabulary.id;

# Update index
ALTER TABLE users_vocabulary DROP INDEX user_vocabulary;
ALTER TABLE users_vocabulary ADD UNIQUE KEY `user_vocabulary` (`user_id`,`vocabulary_id`);