DELIMITER //

DROP FUNCTION IF EXISTS murmur_hash_v3 //

CREATE FUNCTION `murmur_hash_v3`(`key_char` TEXT, `seed` int unsigned)
    RETURNS int unsigned
    DETERMINISTIC
BEGIN
    DECLARE keyx BLOB;
    DECLARE remainder,bytes,c1,c2,i, m1,m2 INT unsigned;
    DECLARE h1,k1,h1b BIGINT unsigned;
    SET keyx = CAST(key_char AS BINARY);
    SET remainder = LENGTH(keyx) & 3;
    SET bytes = LENGTH(keyx) - remainder;
    SET h1 = seed;
    SET c1 = 0xcc9e2d51;
    SET c2 = 0x1b873593;
    SET m1 = 0x85ebca6b, m2 = 0xc2b2ae35;
    SET i = 1;

    WHILE i <= bytes DO
        SET k1 =
             (ascii(mid(keyx,i , 1)) & 0xff)        |
            ((ascii(mid(keyx,i+1,1)) & 0xff) << 8)  |
            ((ascii(mid(keyx,i+2,1)) & 0xff) << 16) |
            ((ascii(mid(keyx,i+3,1)) & 0xff) << 24)
          ;
        SET i = i + 4;

        SET k1 = (k1*c1) & 0xffffffff;
        SET k1 = ((k1 << 15) | (k1 >> 17))& 0xffffffff;
        SET k1 = (k1*c2) & 0xffffffff;

        SET h1 = h1 ^ k1;
        SET h1 = ((h1 << 13) | (h1 >> 19))& 0xffffffff;
        SET h1b = (h1*5) & 0xffffffff;
        SET h1 = (h1b+0xe6546b64)& 0xffffffff;
    END WHILE;

    SET k1 = 0;

    IF remainder>=3 THEN SET k1 = k1^((ascii(mid(keyx,i + 2,1)) & 0xff) << 16); END IF;
    IF remainder>=2 THEN SET k1 = k1^((ascii(mid(keyx,i + 1,1)) & 0xff) <<  8); END IF;
    IF remainder>=1 THEN SET k1 = k1^((ascii(mid(keyx,i + 0,1)) & 0xff) <<  0);
        SET k1 = (k1*c1) & 0xffffffff;
        SET k1 = ((k1 << 15) | (k1 >> 17))& 0xffffffff;
        SET k1 = (k1*c2) & 0xffffffff;
        SET h1 = h1 ^ k1;
    END IF;

    SET h1 = h1 ^ LENGTH(keyx);
    SET h1 = h1 ^ (h1 >> 16);
    SET h1 = (h1*m1) & 0xffffffff;

    SET h1 = h1 ^ (h1 >> 13);
    SET h1 = (h1*m2) & 0xffffffff;
    SET h1 = h1 ^ (h1 >> 16);

    RETURN h1;
END;
//
DELIMITER ;