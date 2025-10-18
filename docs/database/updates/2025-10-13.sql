-- last_contribution  Timestamp of the last time the user added a sentence.
ALTER TABLE users
  ADD `last_contribution` int(11) NOT NULL DEFAULT '0';