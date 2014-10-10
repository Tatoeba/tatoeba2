from django.core.management.base import BaseCommand
from tatoeba2.models import Sentences, SentenceComments, SentencesTranslations, Contributions, Users, TagsSentences, SentencesSentencesLists, FavoritesUsers, SentenceAnnotations
from collections import defaultdict
from datetime import datetime
from itertools import chain
from optparse import make_option
from django.db import transaction
import time


class Dedup(object):

    @staticmethod
    def tally(sents):
        tally = defaultdict(set)
        for sent in sents:
                tally[(sent.text, sent.lang)].add(sent.id)

        return tally
        
    @staticmethod
    def chunked_ranges(chunks, total):
        increment = total / chunks if total % chunks is not 0 else total/chunks - 1
        for chunk in xrange(1, chunks+1):
            frm = chunk + increment * (chunk - 1)
            to = frm + increment if frm < total else total
            yield [frm, to]

    @classmethod
    def prioritize(cls, sents):
        sents = sorted(sents, key=lambda x: x.id)

        cls.has_audio = set()
        cls.has_owner = set()
        cls.not_approved = False
        
        for sent in sents:

            # filter sents with audio
            if sent.hasaudio == 'from_users' or sent.hasaudio == 'shtooka':
                cls.has_audio.add(sent)

            # filter sents with owners
            if sent.user_id:
                cls.has_owner.add(sent)

            # filter unapproved sents
            if sent.correctness is -1:
                cls.not_approved = True

        # has_audio, lowest id
        if cls.has_audio:
            main_sent = sorted(list(cls.has_audio), key=lambda x: x.id)[0]

        # has_owner, lowest id
        elif cls.has_owner:
            main_sent = sorted(list(cls.has_owner), key=lambda x: x.id)[0]

        # fallback, lowest id
        else:
            main_sent = sents[0]

        return main_sent

    @staticmethod
    @transaction.atomic
    def merge_comments(main_id, ids):
        comments = list(SentenceComments.objects.filter(sentence_id__in=ids))
        for comment in comments:
            comment.id = None
            comment.sentence_id = main_id
        SentenceComments.objects.bulk_create(comments)

    @staticmethod
    @transaction.atomic
    def merge_tags(main_id, ids):
        TagsSentences.objects.filter(sentence_id__in=ids).update(sentence_id=main_id)

    @staticmethod
    @transaction.atomic
    def merge_lists(main_id, ids):
        SentencesSentencesLists.objects.filter(sentence_id__in=ids).update(sentence_id=main_id)

    @staticmethod
    @transaction.atomic
    def merge_favorites(main_id, ids):
        FavoritesUsers.objects.filter(favorite_id__in=ids).update(favorite_id=main_id)

    @staticmethod
    @transaction.atomic
    def merge_annotations(main_id, ids):
        SentenceAnnotations.objects.filter(sentence_id__in=ids).update(sentence_id=main_id)

    @classmethod
    def log_link(cls, sent_id, tran_id, action):
        return Contributions(
                    sentence_id=sent_id,
                    translation_id=tran_id,
                    action=action,
                    type='link',
                    datetime=datetime.now(),
                    user_id=cls.bot.id,
               )
   
    @classmethod
    @transaction.atomic
    def merge_links(cls, main_id, ids):
        cls.lnks_fd = SentencesTranslations.objects.filter(sentence_id__in=ids)
        cls.lnks_bd = SentencesTranslations.objects.filter(translation_id__in=ids)

        # slight code duplication to account for future changes in link symmetry
        logs = []

        for lnk in list(cls.lnks_fd):
            logs.append(cls.log_link(lnk.sentence_id, lnk.translation_id, 'delete'))
            logs.append(cls.log_link(main_id, lnk.translation_id, 'insert'))

        for lnk in list(cls.lnks_bd):
            logs.append(cls.log_link(lnk.sentence_id, lnk.translation_id, 'delete'))
            logs.append(cls.log_link(lnk.sentence_id, main_id, 'insert'))

        cls.lnks_fd.update(sentence_id=main_id)
        cls.lnks_bd.update(translation_id=main_id)
        Contributions.objects.bulk_create(logs)

    @classmethod
    @transaction.atomic
    def delete_and_log(cls, ids):
        sents = Sentences.objects.filter(id__in=ids)
        logs = []

        for sent in sents:
            logs.append(Contributions(
                sentence_id=sent.id,
                sentence_lang=sent.lang,
                text=sent.text,
                action='delete',
                datetime=datetime.now(),
                type='sentence',
                user_id=cls.bot.id,
                ))

        sents.delete()
        Contributions.objects.bulk_create(logs)

    @classmethod
    @transaction.atomic
    def deduplicate(cls, main_sent, ids):
        # merge
        cls.merge_comments(main_sent.id, ids)
        cls.merge_tags(main_sent.id, ids)
        cls.merge_links(main_sent.id, ids)
        
        # delete and log duplicates
        cls.delete_and_log(ids)
        
        # fix correctness if needed
        if cls.not_approved:
            main_sent.correctness = -1
            main_sent.save()

class Command(Dedup, BaseCommand):
    option_list = BaseCommand.option_list + (
        make_option(
            '-f', '--full-scan', action='store', type='int', dest='chunks',
            help='attempts deduplication using a full table scan with `n` queries, runs by default with 10 queries'
            ),
        make_option(
            '-i', '--incremental-scan', action='store', type='string', dest='since',
            help='attempts deduplication using an incremental table scan with 1 query filtering sentences added between now and date `d` in `yyyy-mm-dd` format, then a query per row to find duplicates. DO NOT USE THIS WITHOUT A (text, lang) INDEX.'
            ),
        make_option(
            '-p', '--transaction-pause', action='store', type='int', 
            dest='pause_for', help='adds a pause for `n` seconds between deduplication transactions for better rate control.'
            ),
        make_option(
            '-b', '--bot-username', action='store', type='string', dest='bot_name',
            help='username used to log deduplication operations in the contribution table and on the wall'
            ),
        )

    def handle(self, *args, **options):

        if options.get('chunks') and options.get('chunks'):
            print 'conflicting options...'
            return
      
        chunks = options.get('chunks') or 10
        since = options.get('since')

        bot_name = options.get('bot_name') or 'deduplication_bot'
        try:
            self.bot = Users.objects.get(username=bot_name)
        except Users.DoesNotExist:
            self.bot = Users.objects.create(
                username=bot_name, password='', email='bot@bots.com',
                since=datetime.now(), last_time_active=datetime.now().strftime('%Y%m%d'),
                level=1, is_public=1, send_notifications=0, group_id=1
                )

        pause_for = options.get('pause_for') or 0
        self.dup_cnt = 0
        self.dedup_cnt = 0

        # incremental vs full scan routes
        if since:
            # parse date
            since = datetime(*[int(s) for s in since.split('-')])
            # pull in rows from time range
            sents = list(Sentences.objects.filter(created__range=[since, datetime.now()]))
            
            # tally to eliminate premature duplicates
            sent_tally = self.tally(sents)
            del sents

            # filter out duplicates (could probably be done in 1 raw query...)
            dup_set = []            
            for text, lang in sent_tally.iterkeys():
                sents = list(Sentences.objects.filter(text=text, lang=lang))            
                if len(sents) > 1:
                    dup_set.append(sents)

            # deduplicate
            for sents in dup_set:
                # determine main sentence based on priority rules
                main_sent = self.prioritize(sents)
                # separate duplicates from main sentence
                self.dup_cnt += len(sents)
                sents.remove(main_sent)
                # filter out ids
                ids = [sent.id for sent in sents]
                # run a deduplication transaction
                self.deduplicate(main_sent, ids)
                self.dedup_cnt += 1
                # handle rate limiting
                if pause_for: time.sleep(pause_for)
                        
        else:
            # pull in sentences from db in chunks
            total = Sentences.objects.order_by('-id')[0].id
            sents = []
            for rng in self.chunked_ranges(chunks, total):
                sents += list(Sentences.objects.filter(id__range=rng)) # force the orm to evaluate

            sent_tally = self.tally(sents)
            del sents

            # deduplicate
            for ids in sent_tally.itervalues():
                if len(ids) > 1:
                    # pull in needed rows
                    sents = list(Sentences.objects.filter(id__in=ids))
                    
                    main_sent = self.prioritize(sents)

                    # separate duplicates from main sent
                    self.dup_cnt += len(sents)
                    sents.remove(main_sent)
                    ids.remove(main_sent.id)
                    
                    # run a deduplication transaction
                    self.deduplicate(main_sent, ids)
                    self.dedup_cnt += 1
                    # handle rate limit
                    if pause_for: time.sleep(pause_for)
