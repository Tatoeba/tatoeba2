-- Adding id column to sentences_translations because CakePHP
-- requires an primary key when using the model->save() function.
ALTER TABLE sentences_translations AUTO_INCREMENT = 1;
ALTER TABLE sentences_translations 
  ADD COLUMN `id` INT NOT NULL AUTO_INCREMENT FIRST,
  ADD PRIMARY KEY (`id`);


-- Removing auto_increment on tags_sentences.tag_id.
-- (why the hell was there an auto_increment on that field...?)
ALTER TABLE tags_sentences CHANGE tag_id tag_id INT NOT NULL;
-- Adding id column to tags_sentences.
ALTER TABLE tags_sentences AUTO_INCREMENT = 1;
ALTER TABLE tags_sentences
  ADD COLUMN `id` INT NOT NULL AUTO_INCREMENT FIRST,
  ADD PRIMARY KEY (`id`);