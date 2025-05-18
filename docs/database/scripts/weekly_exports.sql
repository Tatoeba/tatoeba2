-- Files that are exported every week on Saturday, at 9AM.

-- Sentences base
SELECT s.id, s.based_on_id
FROM sentences s
WHERE correctness > -1 AND license != ''
INTO OUTFILE '/var/tmp/sentences_base.csv';

-- WWWJDIC indices (also called "B lines")
SELECT sentence_id, meaning_id, text FROM `sentence_annotations`
INTO OUTFILE '/var/tmp/jpn_indices.csv';

-- Sentences
SELECT id, lang, text FROM `sentences`
WHERE correctness > -1 AND license != ''
INTO OUTFILE '/var/tmp/sentences.csv';

-- Sentences with more data
SELECT s.id, s.lang, s.text, u.username, s.created, s.modified
FROM `sentences` s LEFT JOIN `users` u ON s.user_id = u.id
WHERE correctness > -1 AND license != ''
INTO OUTFILE '/var/tmp/sentences_detailed.csv';

-- Links between sentences
SELECT sentence_id, translation_id FROM `sentences_translations`
INTO OUTFILE '/var/tmp/links.csv';

-- Sentences tags
SELECT DISTINCT ts.sentence_id, t.name FROM `tags` t JOIN `tags_sentences` ts
  ON t.id = ts.tag_id
INTO OUTFILE '/var/tmp/tags.csv';

-- Sentence lists
SELECT sl.id, u.username, sl.created, sl.modified, sl.name, sl.editable_by
FROM sentences_lists sl LEFT JOIN users u ON sl.user_id = u.id
WHERE sl.visibility != 'private'
ORDER BY sl.id ASC
INTO OUTFILE '/var/tmp/user_lists.csv';

-- Sentences in lists
SELECT sl.id, s_sl.sentence_id
FROM sentences_sentences_lists s_sl
     JOIN sentences_lists sl ON s_sl.sentences_list_id = sl.id
WHERE sl.visibility != 'private'
ORDER BY sl.id ASC, s_sl.sentence_id
INTO OUTFILE '/var/tmp/sentences_in_lists.csv';

-- Users
SELECT
  u.id,
  u.username,
  CASE u.role -- backward compatibility crap
    WHEN 'corpus_maintainer' THEN 'moderator'
    WHEN 'advanced_contributor' THEN 'trusted_user'
    WHEN 'contributor' THEN 'user'
    ELSE u.role
  END
FROM users u
WHERE u.role != 'spammer'
ORDER BY u.id ASC
INTO OUTFILE '/var/tmp/users.csv';

-- Tag metadata
SELECT t.id, t.name, u.username, t.created
FROM tags t LEFT JOIN users u ON t.user_id = u.id
ORDER BY t.id
INTO OUTFILE '/var/tmp/tag_metadata.csv';

-- Tags (detailed)
SELECT ts.tag_id, ts.sentence_id, u.username, ts.added_time
FROM tags_sentences ts LEFT JOIN users u ON ts.user_id = u.id
ORDER BY ts.added_time ASC
INTO OUTFILE '/var/tmp/tags_detailed.csv';

-- Contributions
SELECT u.username, c.datetime, c.action, c.type, c.sentence_id,
       c.sentence_lang, c.translation_id, c.text
FROM contributions c LEFT JOIN users u ON c.user_id = u.id
ORDER BY c.datetime ASC
INTO OUTFILE '/var/tmp/contributions.csv';

-- Wall posts
SELECT w.id, u.username, w.parent_id, w.date, w.content
FROM wall w LEFT JOIN users u ON w.owner = u.id
ORDER BY w.id ASC
INTO OUTFILE '/var/tmp/wall_posts.csv';

-- Sentence comments
SELECT sc.id, sc.sentence_id, u.username, sc.created, sc.text
FROM sentence_comments sc LEFT JOIN users u ON sc.user_id = u.id
ORDER BY sc.created ASC
INTO OUTFILE '/var/tmp/sentence_comments.csv';

-- Sentences with audio
SELECT a.sentence_id, a.id, u.username, u.audio_license, u.audio_attribution_url
FROM audios a LEFT JOIN users u on u.id = a.user_id
ORDER BY sentence_id ASC
INTO OUTFILE '/var/tmp/sentences_with_audio.csv';

-- User skill level per language
SELECT ul.language_code, ul.level, u.username, ul.details
FROM users_languages ul INNER JOIN users u ON ul.of_user_id = u.id
ORDER BY ul.language_code ASC, ul.level DESC, u.username ASC
INTO OUTFILE '/var/tmp/user_languages.csv';

-- Users sentences
SELECT u.username, us.sentence_id, us.correctness, us.created, us.modified
FROM users_sentences us INNER JOIN users u ON us.user_id = u.id
INTO OUTFILE '/var/tmp/users_sentences.csv';

-- Sentences under CC0
SELECT s.id, s.lang, s.text, s.modified
FROM sentences s
WHERE correctness > -1 AND license = 'CC0 1.0'
INTO OUTFILE '/var/tmp/sentences_CC0.csv';

-- Transcriptions
SELECT t.sentence_id, s.lang, t.script, IFNULL(u.username,''), t.text
FROM transcriptions t
JOIN sentences s ON s.id = t.sentence_id
LEFT JOIN users u ON u.id = t.user_id
WHERE s.correctness > -1
INTO OUTFILE '/var/tmp/transcriptions.csv';
