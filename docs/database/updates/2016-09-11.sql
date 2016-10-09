ALTER TABLE `sentences` ADD COLUMN `hash` BINARY(16) NOT NULL;
ALTER TABLE `sentences` ADD KEY `hash` (`hash`);

source docs/database/procedures/murmur_hash_v3.sql;

UPDATE sentences SET hash=LOWER(CONV(murmur_hash_v3(CONCAT(lang, text), 0), 10, 32));

# Must update users_vocabulary before updating the vocabulary table!
UPDATE IGNORE users_vocabulary
    INNER JOIN vocabulary ON users_vocabulary.vocabulary_id = vocabulary.id
    SET users_vocabulary.vocabulary_id=LOWER(CONV(murmur_hash_v3(CONCAT(vocabulary.lang, vocabulary.text), 0), 10, 32));

UPDATE IGNORE vocabulary SET id=LOWER(CONV(murmur_hash_v3(CONCAT(lang, text), 0), 10, 32));
