ALTER TABLE `private_messages`
    MODIFY COLUMN `folder` enum('Inbox','Sent','Trash','Drafts') CHARACTER SET utf8 NOT NULL DEFAULT 'Inbox';
ALTER TABLE `private_messages`
    ADD COLUMN `draft_recpts` VARCHAR(255) CHARACTER SET utf8 NOT NULL;
ALTER TABLE `private_messages`
    ADD COLUMN `sent` tinyint(4) NOT NULL DEFAULT '1';
    