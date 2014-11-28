-- Remove unused tables
DROP TABLE IF EXISTS `followers_users`;
DROP TABLE IF EXISTS `sentence_annotations_old`;
-- TODO Apprently we have a table `wall2`, can we delete it?



-- Switch to InnoDB
SET SQL_LOG_BIN = 0;
ALTER TABLE acos ENGINE=InnoDB;
ALTER TABLE aros ENGINE=InnoDB;
ALTER TABLE aros_acos ENGINE=InnoDB;
ALTER TABLE contributions ENGINE=InnoDB;
ALTER TABLE countries ENGINE=InnoDB;
ALTER TABLE favorites_users ENGINE=InnoDB;
ALTER TABLE groups ENGINE=InnoDB;
ALTER TABLE languages ENGINE=InnoDB;
ALTER TABLE last_contributions ENGINE=InnoDB;
ALTER TABLE private_messages ENGINE=InnoDB;
ALTER TABLE sentence_annotations ENGINE=InnoDB;
ALTER TABLE sentence_comments ENGINE=InnoDB;
ALTER TABLE sentences ENGINE=InnoDB;
ALTER TABLE sentences_lists ENGINE=InnoDB;
ALTER TABLE sentences_sentences_lists ENGINE=InnoDB;
ALTER TABLE sentences_translations ENGINE=InnoDB;
ALTER TABLE sinogram_subglyphs ENGINE=InnoDB;
ALTER TABLE sinograms ENGINE=InnoDB;
ALTER TABLE sphinx_delta ENGINE=InnoDB;
ALTER TABLE tags ENGINE=InnoDB;
ALTER TABLE tags_sentences ENGINE=InnoDB;
ALTER TABLE users ENGINE=InnoDB;
ALTER TABLE visitors ENGINE=InnoDB;
ALTER TABLE wall ENGINE=InnoDB;
ALTER TABLE wall_threads_last_message ENGINE=InnoDB;
SET SQL_LOG_BIN = 1;
