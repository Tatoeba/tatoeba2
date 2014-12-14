from tatoeba2.management.commands.deduplicate import Command, Dedup
from tatoeba2.models import Sentences, SentenceComments, SentencesTranslations, Contributions, TagsSentences, SentencesSentencesLists, FavoritesUsers, SentenceAnnotations, Contributions, Users, Wall, Languages
from django.db import transaction, IntegrityError
from django.db.models import Q
from hashlib import sha1
import pytest
import os
import logging

def raises(func, exception=Exception, *args, **kwargs):
    raised = False

    try:
        func(*args, **kwargs)
    except exception:
        raised = True

    return raised

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

        Dedup.str_log.info('test')
        assert Dedup.report.getvalue() == 'test\n'

        os.remove(Dedup.log_file_path)

    def test_chunked_ranges(db, dedup):
        assert \
        [rng for rng in dedup.chunked_ranges(1, 5)] == \
        [[1,5]]

        assert \
        [rng for rng in dedup.chunked_ranges(4, 10)] == \
        [[1,3], [4, 6], [7, 9], [10, 10]]

        assert \
        [rng for rng in dedup.chunked_ranges(4, 12)] == \
        [[1, 3], [4, 6], [7, 9], [10, 12]]

    def test_tally(db, sents, dedup):
        sents = list(Sentences.objects.all())
        sents = sents = [(int(sha1(sent.text).hexdigest(), 16), sent.lang, sent.id) for sent in sents]
        sent_tally = dedup.tally(sents)
 
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

    def test_prioritize(db, sents, dedup):
        sents = list(Sentences.objects.filter(id__range=[2, 4]))
        assert dedup.prioritize(sents).id == 2
        
        sents = list(Sentences.objects.filter(id__range=[6, 8]))
        assert dedup.prioritize(sents).id == 8
        
        sents = list(Sentences.objects.filter(id__range=[10, 12]))
        assert dedup.prioritize(sents).id == 12

        sents = list(Sentences.objects.filter(id__range=[14, 16]))
        assert dedup.prioritize(sents).id == 14
        assert dedup.not_approved is True

        sents = list(Sentences.objects.filter(id__range=[18, 21]))
        assert dedup.prioritize(sents).id == 20
        assert dedup.not_approved is True
   
    def test_merge_comments(db, sents, dedup):
        assert SentenceComments.objects.filter(sentence_id=8).count() == 1
        assert SentenceComments.objects.all().count() == 3
        dedup.insert_merge('SentenceComments', 8, [6, 7])
        assert SentenceComments.objects.filter(sentence_id=8).count() == 3
        assert SentenceComments.objects.all().count() == 5
        
        for i in xrange(6, 7+1):
            assert SentenceComments.objects.filter(sentence_id=i).count() == 1
            comments = list(SentenceComments.objects.filter(text='Comment on '+str(i)).order_by('sentence_id'))
            assert len(comments) == 2
            assert comments[0].sentence_id == i
            assert comments[1].sentence_id == 8

    def test_merge_logs(db, sents, dedup):
        assert Contributions.objects.filter(sentence_id=8).count() == 2
        assert Contributions.objects.all().count() == 5
        dedup.merge_logs(8, [6, 7])
        assert Contributions.objects.filter(sentence_id=8).count() == 4
        assert Contributions.objects.all().count() == 7
        
        assert Contributions.objects.filter(sentence_id=6).count() == 2
        logs = list(Contributions.objects.filter(text='Logs for 6').order_by('sentence_id'))
        assert len(logs) == 4
        assert logs[0].sentence_id == 6 and logs[0].type == 'sentence' and logs[0].action == 'update'
        assert logs[1].sentence_id == 6 and logs[1].type == 'link' and logs[1].action == 'insert' and logs[1].translation_id == 9
        assert logs[2].sentence_id == 8 and logs[0].type == 'sentence' and logs[0].action == 'update'
        assert logs[3].sentence_id == 8 and logs[1].type == 'link' and logs[1].action == 'insert' and logs[1].translation_id == 9

    def test_merge_tags(db, sents, dedup):
        assert TagsSentences.objects.filter(sentence_id=8).count() == 1
        dedup.update_merge('TagsSentences', 8, [6, 7])
        assert TagsSentences.objects.filter(sentence_id=8).count() == 3
        for tag in TagsSentences.objects.all(): assert tag.sentence_id == 8

    def test_merge_lists(db, sents, dedup):
        assert SentencesSentencesLists.objects.filter(sentence_id=8).count() == 1
        dedup.update_merge('SentencesSentencesLists', 8, [6, 7])
        assert SentencesSentencesLists.objects.filter(sentence_id=8).count() == 3
        for sent_lst in SentencesSentencesLists.objects.all(): assert sent_lst.sentence_id == 8

    def test_merge_favorites(db, sents, dedup):
        assert FavoritesUsers.objects.filter(favorite_id=8).count() == 1
        dedup.update_merge('FavoritesUsers', 8, [6, 7], 'favorite_id')
        assert FavoritesUsers.objects.filter(favorite_id=8).count() == 3
        for fav in FavoritesUsers.objects.all(): assert fav.favorite_id == 8

    def test_merge_annotations(db, sents, dedup):
        assert SentenceAnnotations.objects.filter(sentence_id=8).count() == 1
        dedup.update_merge('SentenceAnnotations', 8, [6, 7])
        dedup.update_merge('SentenceAnnotations', 10, [11, 12], 'meaning_id')
        assert SentenceAnnotations.objects.filter(sentence_id=8).count() == 3
        assert SentenceAnnotations.objects.filter(meaning_id=10).count() == 3

    def test_merge_links(db, sents, dedup):

        assert SentencesTranslations.objects.filter(sentence_id=8).count() == 0
        dedup.merge_links(8, [6, 7])

        lnks_fd = SentencesTranslations.objects.filter(sentence_id=8).order_by('translation_id')
        assert lnks_fd.count() == 2
        assert lnks_fd[0].sentence_id == 8 and lnks_fd[0].translation_id == 9
        assert lnks_fd[1].sentence_id == 8 and lnks_fd[1].translation_id == 10

        contrib_fd_del = Contributions.objects.filter(sentence_id__in=[6,7], type='link', action='delete').order_by('translation_id')
        assert contrib_fd_del.count() == 2
        assert contrib_fd_del[0].sentence_id == 6 and contrib_fd_del[0].translation_id == 9
        assert contrib_fd_del[1].sentence_id == 7 and contrib_fd_del[1].translation_id == 10

        contrib_fd_ins = Contributions.objects.filter(sentence_id=8, type='link', action='insert').order_by('translation_id')
        assert contrib_fd_ins.count() == 2
        assert contrib_fd_ins[0].sentence_id == 8 and contrib_fd_ins[0].translation_id == 9
        assert contrib_fd_ins[1].sentence_id == 8 and contrib_fd_ins[1].translation_id == 10

        lnks_bd = SentencesTranslations.objects.filter(translation_id=8).order_by('sentence_id')
        assert lnks_bd.count() == 2
        assert lnks_bd[0].sentence_id == 9 and lnks_bd[0].translation_id == 8
        assert lnks_bd[1].sentence_id == 10 and lnks_bd[1].translation_id == 8

        contrib_bd_del = Contributions.objects.filter(translation_id__in=[6,7], type='link', action='delete').order_by('sentence_id')
        assert contrib_bd_del.count() == 2
        assert contrib_bd_del[0].sentence_id == 9 and contrib_bd_del[0].translation_id == 6
        assert contrib_bd_del[1].sentence_id == 10 and contrib_bd_del[1].translation_id == 7

        contrib_bd_ins = Contributions.objects.filter(translation_id=8, type='link', action='insert').order_by('sentence_id')
        assert contrib_bd_ins.count() == 2
        assert contrib_bd_ins[0].sentence_id == 9 and contrib_bd_ins[0].translation_id == 8
        assert contrib_bd_ins[1].sentence_id == 10 and contrib_bd_ins[1].translation_id == 8

    def test_delete_sents(db, sents, dedup):
        assert Sentences.objects.filter(id__in=[6,7]).count() == 2
        assert Contributions.objects.filter(sentence_id__in=[6, 7], type='sentence', action='delete').count() == 0
        dedup.delete_sents(8, [6, 7])
        assert Sentences.objects.filter(id__in=[6,7]).count() == 0
        assert Contributions.objects.filter(sentence_id__in=[6, 7], type='sentence', action='delete').count() == 2

    def test_full_scan(db, sents):
        assert Sentences.objects.all().count() == 21
        cmd = Command()
        cmd.handle()
        assert Sentences.objects.all().count() == 10
        assert len(cmd.all_dups) == 11
        assert len(cmd.all_mains) == 5
        assert cmd.ver_dups
        assert cmd.ver_audio
        assert cmd.ver_mains

    def test_incremental_scan(db, sents):
        assert Sentences.objects.all().count() == 21
        cmd = Command()
        cmd.handle(since='2014-1-4')
        assert Sentences.objects.all().count() == 16
        assert len(cmd.all_dups) == 5
        assert len(cmd.all_mains) == 2
        assert cmd.ver_dups
        assert cmd.ver_audio
        assert cmd.ver_mains

    def test_wall_post(db, sents):
        cmd = Command()
        cmd.handle(wall=True)
        assert Wall.objects.all().count() == 2
        w = Wall.objects.all().order_by('-lft')
        assert w[0].lft == 3 and w[0].rght == 4
        assert w[1].lft == 1 and w[1].rght == 2

    def test_comment_post(db, sents):
        cmd = Command()
        cmd.handle(cmnt=True)
        assert SentenceComments.objects.filter(text__contains='has been merged with').count() == 11

    def test_dry_run(db, sents):
        cmd = Command()
        cmd.handle(dry=True, cmnt=True, wall=True)
        assert Sentences.objects.all().count() == 21
        assert Contributions.objects.all().count() == 5
        assert SentenceComments.objects.all().count() == 3
        assert Users.objects.all().count() == 0
        assert Wall.objects.all().count() == 1

    def test_linked_dups_merge(db, sents, linked_dups, dedup):
        assert not raises(
            dedup.merge_links, IntegrityError,
            2, [3, 4]
            )
        lnks = list(SentencesTranslations.objects.filter(sentence_id=2).order_by('translation_id'))
        assert len(lnks) == 3
        assert lnks[0].sentence_id == 2 and lnks[0].translation_id == 1
        assert lnks[1].sentence_id == 2 and lnks[1].translation_id == 5
        assert lnks[2].sentence_id == 2 and lnks[2].translation_id == 6
        lnks = list(SentencesTranslations.objects.filter(translation_id=2).order_by('sentence_id'))
        assert len(lnks) == 3
        assert lnks[0].sentence_id == 1 and lnks[0].translation_id == 2
        assert lnks[1].sentence_id == 5 and lnks[1].translation_id == 2
        assert lnks[2].sentence_id == 6 and lnks[2].translation_id == 2

    def test_dups_in_list(db, sents, dups_in_list, dedup):
        assert not raises(
            dedup.update_merge, IntegrityError,
            'SentencesSentencesLists', 2, [3, 4]
            )
        lst = list(SentencesSentencesLists.objects.filter(sentence_id=2))
        assert len(lst) == 1
        assert lst[0].sentence_id == 2 and lst[0].sentences_list_id == 4

    def test_dups_in_fav(db, sents, dups_in_fav, dedup):
        assert not raises(
            dedup.update_merge, IntegrityError,
            'FavoritesUsers', 2, [3, 4], 'favorite_id'
            )
        fav = list(FavoritesUsers.objects.filter(favorite_id=2))
        assert len(fav) == 1
        assert fav[0].favorite_id == 2 and fav[0].user_id == 1

    def test_linked_dups_in_logs(db, sents, duplnks_in_logs, dedup):
        dedup.merge_logs(2, [3])
        assert Contributions.objects.filter(sentence_id=2, translation_id=2, type='link').count() == 0

    def test_refresh_lang_stats(db, sents, lang_stats):
        assert Languages.objects.filter(code='eng')[0].numberofsentences == 0
        cmd = Command()
        cmd.handle()
        assert Languages.objects.filter(code='eng')[0].numberofsentences == 10
