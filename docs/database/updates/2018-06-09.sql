-- Maybe backup login credentials if anything goes wrong
-- CREATE TABLE users_bak LIKE users;
-- INSERT users_bak SELECT * FROM users;
ALTER TABLE users CHANGE password password VARCHAR(62);
