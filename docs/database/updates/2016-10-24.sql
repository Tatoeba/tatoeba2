UPDATE users_sentences
    LEFT OUTER JOIN sentences ON(sentences.id=users_sentences.sentence_id)
    SET `dirty` = true
    WHERE sentences.modified > users_sentences.modified;
