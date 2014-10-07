from tatoeba2.management.commands.deduplicate import Dedup, Command
from tatoeba2.models import Sentences, SentenceComments, SentencesTranslations, Contributions
from django.db import transaction
import pytest


@pytest.mark.django_db
class TestDedup():

    def test_baseline(db, sents):
        s = Sentences.objects.get(id=1)
        assert s.text == 'Normal, not duplicated.'

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
        Dedup.merge_comments(8, [6, 7])
        assert SentenceComments.objects.filter(sentence_id=8).count() == 3
        for i in xrange(6, 8+1):
            assert SentenceComments.objects.get(text='Comment on '+str(i)).sentence_id == 8

    def test_merge_links(db, sents, bot):
        Dedup.bot = bot
        assert SentencesTranslations.objects.filter(sentence_id=8).count() == 0
        Dedup.merge_links(8, [6, 7])

        lnks_fd = SentencesTranslations.objects.filter(sentence_id=8).order_by('translation_id')
        assert lnks_fd.count() == 2
        assert lnks_fd[0].sentence_id == 8 and lnks_fd[0].translation_id == 9
        assert lnks_fd[1].sentence_id == 8 and lnks_fd[1].translation_id == 10
        
        unlnks_fd = Contributions.objects.filter(sentence_id__in=[6, 7], type='link', action='delete').order_by('sentence_id')
        assert unlnks_fd.count() == 2
        assert unlnks_fd[0].sentence_id == 6 and unlnks_fd[0].translation_id == 9
        assert unlnks_fd[1].sentence_id == 7 and unlnks_fd[1].translation_id == 10
        
        relnks_fd = Contributions.objects.filter(sentence_id=8, type='link', action='insert').order_by('translation_id')
        assert relnks_fd.count() == 2
        assert relnks_fd[0].sentence_id == 8 and relnks_fd[0].translation_id == 9
        assert relnks_fd[1].sentence_id == 8 and relnks_fd[1].translation_id == 10

        lnks_bd = SentencesTranslations.objects.filter(translation_id=8).order_by('sentence_id')
        assert lnks_bd.count() == 2
        assert lnks_bd[0].sentence_id == 9 and lnks_bd[0].translation_id == 8
        assert lnks_bd[1].sentence_id == 10 and lnks_bd[1].translation_id == 8
        
        unlnks_bd = Contributions.objects.filter(translation_id__in=[6, 7], type='link', action='delete').order_by('translation_id')
        assert unlnks_bd.count() == 2
        assert unlnks_bd[0].sentence_id == 9 and unlnks_bd[0].translation_id == 6
        assert unlnks_bd[1].sentence_id == 10 and unlnks_bd[1].translation_id == 7
        
        relnks_bd = Contributions.objects.filter(translation_id=8, type='link', action='insert').order_by('sentence_id')
        assert relnks_bd.count() == 2
        assert relnks_bd[0].sentence_id == 9 and relnks_bd[0].translation_id == 8
        assert relnks_bd[1].sentence_id == 10 and relnks_bd[1].translation_id == 8

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
