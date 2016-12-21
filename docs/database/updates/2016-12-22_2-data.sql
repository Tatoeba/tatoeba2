DELIMITER //
CREATE PROCEDURE AssignLicenseToUser(
  IN contributor VARCHAR(20),
  IN licenseName VARCHAR(50),
  IN attributionUrl VARCHAR(255)
)
  BEGIN
    DECLARE userId INT(11) DEFAULT NULL;

    SELECT id INTO userId FROM users WHERE username = contributor LIMIT 1;
    IF userId IS NOT NULL THEN
      UPDATE users
        SET audio_license = licenseName,
            audio_attribution_url = attributionUrl
      WHERE id = userId;
    END IF;
  END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE AssignAudioToUser(
  IN listId INT(11),
  IN contributor VARCHAR(50)
)
  BEGIN
    DECLARE userId INT(11) DEFAULT NULL;

    SELECT id INTO userId FROM users WHERE username = contributor LIMIT 1;
    IF userId IS NOT NULL THEN
      UPDATE audios
        SET user_id = userId
      WHERE sentence_id IN (
        SELECT sentence_id
        FROM sentences_sentences_lists
        WHERE sentences_list_id = listId
      );
    END IF;
  END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE AssignAudioToExternalUser(
  IN listId INT(11),
  IN authorName VARCHAR(50),
  IN licenseName VARCHAR(50),
  IN attributionUrl VARCHAR(255)
)
  BEGIN
    UPDATE audios
      SET external = CONCAT('{"username":"',authorName,'","license":"',licenseName,'","attribution_url":"',attributionUrl,'"}')
    WHERE sentence_id IN (
      SELECT sentence_id
      FROM sentences_sentences_lists
      WHERE sentences_list_id = listId
    );
  END //
DELIMITER ;

CALL AssignAudioToUser(4000, 'CK');
CALL AssignLicenseToUser('CK', 'CC BY-NC-ND 3.0', 'http://www.manythings.org/tatoeba');
CALL AssignAudioToUser(4007, 'nava');
CALL AssignAudioToUser(4008, 'pon00050');
CALL AssignAudioToUser(4009, 'Dorenda');
CALL AssignAudioToUser(4010, 'hurdsean');
CALL AssignAudioToUser(4011, 'sysko');
CALL AssignAudioToUser(4012, 'BraveSentry');
CALL AssignAudioToUser(4013, 'MUIRIEL');
CALL AssignAudioToUser(4014, 'fucongcong');
CALL AssignAudioToUser(4015, 'namikiri');
CALL AssignLicenseToUser('namikiri', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(4016, 'BE');
CALL AssignLicenseToUser('BE', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(4017, 'LouiseRatty');
CALL AssignAudioToUser(4018, 'Delian');
CALL AssignLicenseToUser('Delian', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(4019, 'mhattick');
CALL AssignAudioToUser(4020, 'mervert1');
CALL AssignAudioToUser(4021, 'fekundulo');
CALL AssignLicenseToUser('fekundulo', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToExternalUser(4022, 'Barack Obama', 'Public domain', 'http://www.whitehouse.gov/briefing-room/weekly-address');
CALL AssignAudioToUser(4023, 'yomi');
CALL AssignLicenseToUser('yomi', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(4024, 'papabear');
CALL AssignAudioToUser(4025, 'Nero');
CALL AssignAudioToUser(4132, 'marcelostockle');
CALL AssignLicenseToUser('marcelostockle', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(4134, 'alexmarcelo');
CALL AssignAudioToUser(4136, 'Ramses');
CALL AssignAudioToUser(4137, 'jarojuda');
CALL AssignAudioToUser(4138, 'alexmarcelo');
CALL AssignAudioToUser(4139, 'Inego');
CALL AssignLicenseToUser('Inego', 'CC BY 4.0', NULL);
CALL AssignAudioToUser(4140, 'Shady_arc');
CALL AssignLicenseToUser('Shady_arc', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(4141, 'hayastan');
CALL AssignLicenseToUser('hayastan', 'CC BY 4.0', NULL);
CALL AssignAudioToUser(4142, 'marcelostockle');
CALL AssignAudioToUser(4143, 'fucongcong');
CALL AssignAudioToUser(4144, 'sacredceltic');
CALL AssignLicenseToUser('sacredceltic', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(4145, 'brauliobezerra');
CALL AssignLicenseToUser('brauliobezerra', 'CC BY-NC 4.0', NULL);
-- CALL AssignAudioToUser(4146, 'UNKNOWN');
-- CALL AssignAudioToUser(4147, 'UNKNOWN Maybe tatoerique');
-- CALL AssignAudioToUser(4148, 'UNKNOWN');
-- CALL AssignAudioToUser(4149, 'UNKNOWN');
CALL AssignAudioToUser(4150, 'Seael');
CALL AssignAudioToUser(4151, 'N0ps32');
CALL AssignAudioToUser(4152, 'ijikure');
CALL AssignAudioToUser(4237, 'Susan1430');
CALL AssignLicenseToUser('Susan1430', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(4645, 'lovermann');
CALL AssignLicenseToUser('lovermann', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(4679, 'yeti');
CALL AssignLicenseToUser('yeti', 'CC BY 4.0', NULL);
CALL AssignAudioToUser(4767, 'PaulP');
CALL AssignLicenseToUser('PaulP', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(4956, 'diane');
CALL AssignAudioToUser(5105, 'mraz');
CALL AssignLicenseToUser('mraz', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(5110, 'gusrudess');
CALL AssignAudioToUser(5174, 'Phoenix');
CALL AssignLicenseToUser('Phoenix', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(5660, 'huizi99');
CALL AssignLicenseToUser('huizi99', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(5748, 'Mizu');
CALL AssignLicenseToUser('Mizu', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(5861, 'Snark');
CALL AssignAudioToUser(6036, 'rhys_mcg');
CALL AssignLicenseToUser('rhys_mcg', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(6053, 'Khamlan');
CALL AssignAudioToUser(6128, 'Tatu');
CALL AssignAudioToUser(6138, 'bill');
CALL AssignLicenseToUser('bill', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(6213, 'gonsalet');
CALL AssignLicenseToUser('gonsalet', 'CC BY-SA 4.0', NULL);
CALL AssignAudioToUser(6229, 'pencil');
CALL AssignLicenseToUser('pencil', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(6258, 'gretelen');
CALL AssignLicenseToUser('gretelen', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(6268, 'VSL56');
CALL AssignAudioToUser(6272, 'patgfisher');
CALL AssignAudioToUser(6274, 'sabretou');
CALL AssignLicenseToUser('sabretou', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(6291, 'jaxhere');
CALL AssignAudioToUser(6313, 'Kritter');
CALL AssignAudioToUser(6318, 'Objectivesea');
CALL AssignLicenseToUser('Objectivesea', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(6329, 'amastan');
CALL AssignAudioToUser(6332, 'civiricus');
CALL AssignAudioToUser(6377, 'Dusun_Les');
CALL AssignAudioToUser(6445, 'peschiber');
CALL AssignLicenseToUser('peschiber', 'CC BY-NC 4.0', NULL);
CALL AssignAudioToUser(6685, 'arh');
CALL AssignLicenseToUser('arh', 'CC BY-NC-ND 3.0', NULL);
CALL AssignAudioToUser(6706, 'juliastef');

DROP PROCEDURE AssignAudioToUser;
DROP PROCEDURE AssignLicenseToUser;
DROP PROCEDURE AssignAudioToExternalUser;
