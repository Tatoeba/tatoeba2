from tatoeba2.management.commands.deduplicate import Dedup, Command
from tatoeba2.models import Sentences, SentenceComments, SentencesTranslations, Contributions, TagsSentences, SentencesSentencesLists, FavoritesUsers, SentenceAnnotations, Contributions
from django.db import transaction
import pytest
import os
import logging


@pytest.mark.django_db
class TestDedup():

    def test_baseline(db, sents):
        s = Sentences.objects.get(id=1)
        assert s.text == 'Normal, not duplicated.'

    def test_logger(db, sents, capsys):
        Dedup.time_init()
        Dedup.logger_init()

        Dedup.out_log.debug('test')
        Dedup.out_log.info('test')
        out, _ = capsys.readouterr()
        assert out == 'test\n'

        Dedup.out_log.setLevel(logging.DEBUG)
        Dedup.out_log.debug('test')
        Dedup.out_log.info('test')
        out, _ = capsys.readouterr()
        assert out == 'test\ntest\n'
        

        Dedup.file_log.info('test')
        with open(Dedup.log_file_path) as f:
            assert f.read() == 'test\n'
        
        os.remove(Dedup.log_file_path)

        Dedup.str_log.info('test')
        assert Dedup.report.getvalue() == 'test\n'

    def test_chunked_ranges(db):
        assert \
        [rng for rng in Dedup.chunked_ranges(1, 5)] == \
        [[1,5]]

        assert \
        [rng for rng in Dedup.chunked_ranges(4, 10)] == \
        [[1,3], [4, 6], [7, 9], [10, 10]]

        assert \
        [rng for rng in Dedup.chunked_ranges(4, 12)] == \
        [[1, 3], [4, 6], [7, 9], [10, 12]]

    def test_tally(db, sents):
        sent_tally = Dedup.tally(Sentences.objects.all())
 
        k_cnt = 0
        for k, v in sent_tally.iteritems():
            if k == ('Has owner, Has audio, Correctness -1 duplicated.', 'eng'):
                assert len(v) == 4

            if k == ('Normal, duplicated.', 'eng') or \
               k == ('Has owner, duplicated.', 'eng') or \
               k == ('Has audio, duplicated.', 'eng') or \
               k == ('Correctness -1, duplicated.', 'eng'):
                assert len(v) == 3

            if k == ('Normal, not duplicated.', 'eng') or \
               k == ('Has owner, not duplicated.', 'eng') or \
               k == ('Has audio, not duplicated.', 'eng') or \
               k == ('Correctness -1, not duplicated.', 'eng'):
                assert len(v) == 1

            k_cnt += 1

        assert k_cnt == 10

    def test_prioritize(db, sents):
        sents = list(Sentences.objects.filter(id__range=[2, 4]))
        assert Dedup.prioritize(sents).id == 2
        
        sents = list(Sentences.objects.filter(id__range=[6, 8]))
        assert Dedup.prioritize(sents).id == 8
        
        sents = list(Sentences.objects.filter(id__range=[10, 12]))
        assert Dedup.prioritize(sents).id == 12

        sents = list(Sentences.objects.filter(id__range=[14, 16]))
        assert Dedup.prioritize(sents).id == 14
        assert Dedup.not_approved is True

        sents = list(Sentences.objects.filter(id__range=[18, 21]))
        assert Dedup.prioritize(sents).id == 20
        assert Dedup.not_approved is True
   
    def test_merge_comments(db, sents):
        assert SentenceComments.objects.filter(sentence_id=8).count() == 1
        assert SentenceComments.objects.all().count() == 3
        Dedup.merge_comments(8, [6, 7])
        assert SentenceComments.objects.filter(sentence_id=8).count() == 3
        assert SentenceComments.objects.all().count() == 5
        
        for i in xrange(6, 7+1):
            assert SentenceComments.objects.filter(sentence_id=i).count() == 1
            comments = list(SentenceComments.objects.filter(text='Comment on '+str(i)).order_by('sentence_id'))
            assert len(comments) == 2
            assert comments[0].sentence_id == i
            assert comments[1].sentence_id == 8

    def test_merge_logs(db, sents):
        assert Contributions.objects.filter(sentence_id=8).count() == 1
        assert Contributions.objects.all().count() == 3
        Dedup.merge_logs(8, [6, 7])
        assert Contributions.objects.filter(sentence_id=8).count() == 3
        assert Contributions.objects.all().count() == 5
        
        for i in xrange(6, 7+1):
            assert Contributions.objects.filter(sentence_id=i).count() == 1
            logs = list(Contributions.objects.filter(text='Logs for '+str(i)).order_by('sentence_id'))
            assert len(logs) == 2
            assert logs[0].sentence_id == i
            assert logs[1].sentence_id == 8

    def test_merge_tags(db, sents):
        assert TagsSentences.objects.filter(sentence_id=8).count() == 1
        Dedup.merge_tags(8, [6, 7])
        assert TagsSentences.objects.filter(sentence_id=8).count() == 3
        for tag in TagsSentences.objects.all(): assert tag.sentence_id == 8

    def test_merge_lists(db, sents):
        assert SentencesSentencesLists.objects.filter(sentence_id=8).count() == 1
        Dedup.merge_lists(8, [6, 7])
        assert SentencesSentencesLists.objects.filter(sentence_id=8).count() == 3
        for sent_lst in SentencesSentencesLists.objects.all(): assert sent_lst.sentence_id == 8

    def test_merge_favorites(db, sents):
        assert FavoritesUsers.objects.filter(favorite_id=8).count() == 1
        Dedup.merge_favorites(8, [6, 7])
        assert FavoritesUsers.objects.filter(favorite_id=8).count() == 3
        for fav in FavoritesUsers.objects.all(): assert fav.favorite_id == 8

    def test_merge_annotations(db, sents):
        assert SentenceAnnotations.objects.filter(sentence_id=8).count() == 1
        Dedup.merge_annotations(8, [6, 7])
        assert SentenceAnnotations.objects.filter(sentence_id=8).count() == 3
        for ann in SentenceAnnotations.objects.all(): assert ann.sentence_id == 8

    def test_merge_links(db, sents, bot):
        Dedup.time_init()
        Dedup.logger_init()
        Dedup.bot = bot

        assert SentencesTranslations.objects.filter(sentence_id=8).count() == 0
        Dedup.merge_links(8, [6, 7])

        lnks_fd = SentencesTranslations.objects.filter(sentence_id=8).order_by('translation_id')
        assert lnks_fd.count() == 2
        assert lnks_fd[0].sentence_id == 8 and lnks_fd[0].translation_id == 9
        assert lnks_fd[1].sentence_id == 8 and lnks_fd[1].translation_id == 10

        lnks_bd = SentencesTranslations.objects.filter(translation_id=8).order_by('sentence_id')
        assert lnks_bd.count() == 2
        assert lnks_bd[0].sentence_id == 9 and lnks_bd[0].translation_id == 8
        assert lnks_bd[1].sentence_id == 10 and lnks_bd[1].translation_id == 8

    def test_delete_and_log(db, sents, bot):
        Dedup.bot = bot
        assert Sentences.objects.filter(id__in=[6,7]).count() == 2
        assert Contributions.objects.filter(sentence_id__in=[6, 7], type='sentence', action='delete').count() == 0
        Dedup.delete_and_log([6, 7])
        assert Sentences.objects.filter(id__in=[6,7]).count() == 0
        assert Contributions.objects.filter(sentence_id__in=[6, 7], type='sentence', action='delete').count() == 2

    def test_full_scan(db, sents):
        assert Sentences.objects.all().count() == 21
        cmd = Command()
        cmd.handle()
        assert Sentences.objects.all().count() == 10
        assert cmd.dup_cnt == 16
        assert cmd.dedup_cnt == 5

    def test_incremental_scan(db, sents):
        assert Sentences.objects.all().count() == 21
        cmd = Command()
        cmd.handle(since='2014-1-4')
        assert Sentences.objects.all().count() == 16
        assert cmd.dup_cnt == 7
        assert cmd.dedup_cnt == 2
