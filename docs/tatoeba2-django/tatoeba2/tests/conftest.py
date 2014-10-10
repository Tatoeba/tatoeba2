from tatoeba2.models import Sentences, SentenceComments, SentencesTranslations, Users, TagsSentences, SentencesSentencesLists, FavoritesUsers, SentenceAnnotations
from datetime import datetime
import pytest


@pytest.fixture(scope='session')
def sents(db):

    # no owner, no audio, no correctness 1-4
    Sentences(text='Normal, not duplicated.', lang='eng', created=datetime(2014, 1, 1)).save()
    for i in xrange(3): Sentences(text='Normal, duplicated.', lang='eng', created=datetime(2014, 1, 1)).save()

    # has owner 5-8
    Sentences(text='Has owner, not duplicated.', lang='eng', user_id=1, created=datetime(2014, 1, 2)).save()
    for i in xrange(2): Sentences(text='Has owner, duplicated.', lang='eng', created=datetime(2014, 1, 2)).save()
    Sentences(text='Has owner, duplicated.', lang='eng', user_id=1, created=datetime(2014, 1, 2)).save()

    # has audio 9-12
    Sentences(text='Has audio, not duplicated.', lang='eng', hasaudio='shtooka', created=datetime(2014, 1, 3)).save()
    for i in xrange(2): Sentences(text='Has audio, duplicated.', lang='eng', created=datetime(2014, 1, 3)).save()
    Sentences(text='Has audio, duplicated.', lang='eng', hasaudio='shtooka', created=datetime(2014, 1, 3)).save()

    # correctness -1  13-16
    Sentences(text='Correctness -1, not duplicated.', lang='eng', correctness=-1, created=datetime(2014, 1, 4)).save()
    for i in xrange(2): Sentences(text='Correctness -1, duplicated.', lang='eng', created=datetime(2014, 1, 4)).save()
    Sentences(text='Correctness -1, duplicated.', lang='eng', correctness=-1, created=datetime(2014, 1, 4)).save()
    
    # has owner, has audio, correctness -1  17-21
    Sentences(text='Has owner, Has audio, Correctness -1, not duplicated.', lang='eng', user_id=1, hasaudio='shtooka', correctness=-1, created=datetime(2014, 1, 5)).save()
    Sentences(text='Has owner, Has audio, Correctness -1 duplicated.', lang='eng', created=datetime(2014, 1, 5)).save()
    Sentences(text='Has owner, Has audio, Correctness -1 duplicated.', lang='eng', user_id=1, created=datetime(2014, 1, 5)).save()
    Sentences(text='Has owner, Has audio, Correctness -1 duplicated.', lang='eng', hasaudio='shtooka', created=datetime(2014, 1, 5)).save()
    Sentences(text='Has owner, Has audio, Correctness -1 duplicated.', lang='eng', correctness=-1, created=datetime(2014, 1, 5)).save()
    
    for i in xrange(1, 21+1): SentenceComments(sentence_id=i, text='Comment on '+str(i), user_id=1, created=datetime.now(), hidden=0).save()
    
    SentencesTranslations(sentence_id=6, translation_id=9, distance=1).save()
    SentencesTranslations(sentence_id=9, translation_id=6, distance=1).save()
    SentencesTranslations(sentence_id=7, translation_id=10, distance=1).save()
    SentencesTranslations(sentence_id=10, translation_id=7, distance=1).save()
    
    TagsSentences(tag_id=1, sentence_id=6, user_id=1, added_time=datetime.now()).save()
    TagsSentences(tag_id=2, sentence_id=7, user_id=1, added_time=datetime.now()).save()
    TagsSentences(tag_id=3, sentence_id=8, user_id=1, added_time=datetime.now()).save()
    
    SentencesSentencesLists(sentences_list_id=1, sentence_id=6).save()
    SentencesSentencesLists(sentences_list_id=2, sentence_id=7).save()
    SentencesSentencesLists(sentences_list_id=3, sentence_id=8).save()
    
    FavoritesUsers(user_id=1, favorite_id=6).save()
    FavoritesUsers(user_id=2, favorite_id=7).save()
    FavoritesUsers(user_id=3, favorite_id=8).save()
    
    SentenceAnnotations(meaning_id=1, text='', modified=datetime.now(), user_id=1, sentence_id=6).save()
    SentenceAnnotations(meaning_id=2, text='', modified=datetime.now(), user_id=1, sentence_id=7).save()
    SentenceAnnotations(meaning_id=3, text='', modified=datetime.now(), user_id=1, sentence_id=8).save()

@pytest.fixture()
def bot(db):
    return Users.objects.create(
                username='deduplication_bot', password='', email='bot@bots.com',
                since=datetime.now(), last_time_active=datetime.now().strftime('%Y%m%d'),
                level=1, is_public=1, send_notifications=0, group_id=1
                )
