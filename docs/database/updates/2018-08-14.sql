ALTER TABLE contributions_stats CHANGE type type enum('link','sentence','license') CHARACTER SET latin1 NOT NULL;
ALTER TABLE contributions CHANGE type type enum('link','sentence','license') CHARACTER SET latin1 NOT NULL;
