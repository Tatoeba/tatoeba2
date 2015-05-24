ALTER TABLE users ADD settings BLOB NOT NULL AFTER `description`;

UPDATE users SET settings = CONCAT('{',
    '"is_public":',          IF(is_public,'true','false'),          ',',
    '"send_notifications":', IF(send_notifications,'true','false'), ',',
    '"lang":',               '"',COALESCE(lang,''),'"',
'}');

ALTER TABLE users DROP is_public;
ALTER TABLE users DROP send_notifications;
ALTER TABLE users DROP lang;

