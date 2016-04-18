UPDATE sentences
SET lang = 'arb'
WHERE lang = 'ara';

UPDATE languages
SET code = 'arb'
WHERE code = 'ara';

UPDATE contributions
SET sentence_lang = 'arb'
WHERE sentence_lang = 'ara';

UPDATE contributions
SET translation_lang = 'arb'
WHERE translation_lang = 'ara';

UPDATE last_contributions
SET sentence_lang = 'arb'
WHERE sentence_lang = 'ara';

UPDATE last_contributions
SET translation_lang = 'arb'
WHERE translation_lang = 'ara';

UPDATE sentences_translations
SET sentence_lang = 'arb'
WHERE sentence_lang = 'ara';

UPDATE sentences_translations
SET translation_lang = 'arb'
WHERE translation_lang = 'ara';

UPDATE users_languages
SET language_code = 'arb'
WHERE language_code = 'ara';

UPDATE contributions_stats
SET lang = 'arb'
WHERE lang = 'ara';
