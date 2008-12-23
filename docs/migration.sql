SELECT 0;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 0 AND dico_id < 1001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 1000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 1000 AND dico_id < 2001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 2000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 2000 AND dico_id < 3001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 3000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 3000 AND dico_id < 4001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 4000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 4000 AND dico_id < 5001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 5000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 5000 AND dico_id < 6001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 6000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 6000 AND dico_id < 7001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 7000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 7000 AND dico_id < 8001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 8000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 8000 AND dico_id < 9001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 9000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 9000 AND dico_id < 10001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 10000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 10000 AND dico_id < 11001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 11000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 11000 AND dico_id < 12001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 12000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 12000 AND dico_id < 13001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 13000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 13000 AND dico_id < 14001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 14000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 14000 AND dico_id < 15001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 15000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 15000 AND dico_id < 16001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 16000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 16000 AND dico_id < 17001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 17000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 17000 AND dico_id < 18001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 18000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 18000 AND dico_id < 19001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 19000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 19000 AND dico_id < 20001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 20000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 20000 AND dico_id < 21001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 21000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 21000 AND dico_id < 22001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 22000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 22000 AND dico_id < 23001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 23000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 23000 AND dico_id < 24001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 24000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 24000 AND dico_id < 25001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 25000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 25000 AND dico_id < 26001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 26000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 26000 AND dico_id < 27001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 27000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 27000 AND dico_id < 28001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 28000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 28000 AND dico_id < 29001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 29000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 29000 AND dico_id < 30001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 30000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 30000 AND dico_id < 31001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 31000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 31000 AND dico_id < 32001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 32000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 32000 AND dico_id < 33001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 33000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 33000 AND dico_id < 34001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 34000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 34000 AND dico_id < 35001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 35000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 35000 AND dico_id < 36001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 36000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 36000 AND dico_id < 37001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 37000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 37000 AND dico_id < 38001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 38000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 38000 AND dico_id < 39001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 39000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 39000 AND dico_id < 40001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 40000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 40000 AND dico_id < 41001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 41000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 41000 AND dico_id < 42001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 42000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 42000 AND dico_id < 43001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 43000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 43000 AND dico_id < 44001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 44000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 44000 AND dico_id < 45001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 45000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 45000 AND dico_id < 46001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 46000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 46000 AND dico_id < 47001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 47000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 47000 AND dico_id < 48001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 48000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 48000 AND dico_id < 49001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 49000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 49000 AND dico_id < 50001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 50000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 50000 AND dico_id < 51001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 51000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 51000 AND dico_id < 52001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 52000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 52000 AND dico_id < 53001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 53000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 53000 AND dico_id < 54001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 54000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 54000 AND dico_id < 55001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 55000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 55000 AND dico_id < 56001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 56000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 56000 AND dico_id < 57001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 57000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 57000 AND dico_id < 58001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 58000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 58000 AND dico_id < 59001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 59000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 59000 AND dico_id < 60001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 60000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 60000 AND dico_id < 61001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 61000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 61000 AND dico_id < 62001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 62000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 62000 AND dico_id < 63001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 63000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 63000 AND dico_id < 64001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 64000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 64000 AND dico_id < 65001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 65000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 65000 AND dico_id < 66001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 66000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 66000 AND dico_id < 67001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 67000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 67000 AND dico_id < 68001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 68000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 68000 AND dico_id < 69001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 69000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 69000 AND dico_id < 70001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 70000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 70000 AND dico_id < 71001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 71000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 71000 AND dico_id < 72001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 72000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 72000 AND dico_id < 73001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 73000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 73000 AND dico_id < 74001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 74000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 74000 AND dico_id < 75001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 75000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 75000 AND dico_id < 76001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 76000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 76000 AND dico_id < 77001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 77000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 77000 AND dico_id < 78001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 78000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 78000 AND dico_id < 79001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 79000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 79000 AND dico_id < 80001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 80000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 80000 AND dico_id < 81001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 81000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 81000 AND dico_id < 82001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 82000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 82000 AND dico_id < 83001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 83000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 83000 AND dico_id < 84001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 84000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 84000 AND dico_id < 85001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 85000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 85000 AND dico_id < 86001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 86000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 86000 AND dico_id < 87001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 87000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 87000 AND dico_id < 88001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 88000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 88000 AND dico_id < 89001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 89000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 89000 AND dico_id < 90001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 90000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 90000 AND dico_id < 91001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 91000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 91000 AND dico_id < 92001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 92000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 92000 AND dico_id < 93001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 93000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 93000 AND dico_id < 94001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 94000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 94000 AND dico_id < 95001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 95000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 95000 AND dico_id < 96001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 96000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 96000 AND dico_id < 97001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 97000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 97000 AND dico_id < 98001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 98000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 98000 AND dico_id < 99001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 99000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 99000 AND dico_id < 100001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 100000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 100000 AND dico_id < 101001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 101000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 101000 AND dico_id < 102001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 102000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 102000 AND dico_id < 103001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 103000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 103000 AND dico_id < 104001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 104000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 104000 AND dico_id < 105001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 105000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 105000 AND dico_id < 106001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 106000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 106000 AND dico_id < 107001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 107000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 107000 AND dico_id < 108001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 108000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 108000 AND dico_id < 109001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 109000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 109000 AND dico_id < 110001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 110000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 110000 AND dico_id < 111001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 111000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 111000 AND dico_id < 112001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 112000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 112000 AND dico_id < 113001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 113000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 113000 AND dico_id < 114001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 114000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 114000 AND dico_id < 115001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 115000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 115000 AND dico_id < 116001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 116000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 116000 AND dico_id < 117001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 117000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 117000 AND dico_id < 118001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 118000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 118000 AND dico_id < 119001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 119000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 119000 AND dico_id < 120001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 120000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 120000 AND dico_id < 121001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 121000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 121000 AND dico_id < 122001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 122000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 122000 AND dico_id < 123001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 123000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 123000 AND dico_id < 124001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 124000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 124000 AND dico_id < 125001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 125000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 125000 AND dico_id < 126001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 126000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 126000 AND dico_id < 127001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 127000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 127000 AND dico_id < 128001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 128000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 128000 AND dico_id < 129001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 129000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 129000 AND dico_id < 130001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 130000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 130000 AND dico_id < 131001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 131000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 131000 AND dico_id < 132001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 132000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 132000 AND dico_id < 133001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 133000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 133000 AND dico_id < 134001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 134000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 134000 AND dico_id < 135001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 135000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 135000 AND dico_id < 136001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 136000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 136000 AND dico_id < 137001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 137000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 137000 AND dico_id < 138001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 138000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 138000 AND dico_id < 139001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 139000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 139000 AND dico_id < 140001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 140000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 140000 AND dico_id < 141001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 141000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 141000 AND dico_id < 142001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 142000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 142000 AND dico_id < 143001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 143000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 143000 AND dico_id < 144001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 144000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 144000 AND dico_id < 145001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 145000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 145000 AND dico_id < 146001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 146000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 146000 AND dico_id < 147001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 147000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 147000 AND dico_id < 148001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 148000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 148000 AND dico_id < 149001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 149000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 149000 AND dico_id < 150001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 150000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 150000 AND dico_id < 151001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 151000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 151000 AND dico_id < 152001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 152000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 152000 AND dico_id < 153001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 153000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 153000 AND dico_id < 154001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 154000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 154000 AND dico_id < 155001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 155000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 155000 AND dico_id < 156001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 156000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 156000 AND dico_id < 157001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 157000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 157000 AND dico_id < 158001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 158000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 158000 AND dico_id < 159001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 159000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 159000 AND dico_id < 160001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 160000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 160000 AND dico_id < 161001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 161000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 161000 AND dico_id < 162001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 162000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 162000 AND dico_id < 163001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 163000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 163000 AND dico_id < 164001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 164000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 164000 AND dico_id < 165001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 165000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 165000 AND dico_id < 166001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 166000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 166000 AND dico_id < 167001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 167000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 167000 AND dico_id < 168001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

SELECT 168000;

DELETE FROM sentences_tmp;

INSERT INTO sentences_tmp
SELECT * FROM sentences
WHERE dico_id > 168000 AND dico_id < 169001;

INSERT INTO sentences_translations(sentence_id, translation_id, distance)
SELECT t1.id, t2.id,1
FROM sentences_tmp t1 INNER JOIN sentences_tmp t2
ON t1.dico_id = t2.dico_id and t1.lang != t2.lang;

