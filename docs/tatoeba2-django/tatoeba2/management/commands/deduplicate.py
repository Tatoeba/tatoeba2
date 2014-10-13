from django.core.management.base import BaseCommand
from django.conf import settings
from tatoeba2.models import Sentences, SentenceComments, SentencesTranslations, Contributions, Users, TagsSentences, SentencesSentencesLists, FavoritesUsers, SentenceAnnotations
from collections import defaultdict
from datetime import datetime
from itertools import chain
from optparse import make_option
from django.db import transaction
from StringIO import StringIO
from os import path
from django.core import serializers
from django.db.models.loading import get_model
import time
import logging
import sys
import json


class Dedup(object):

    @classmethod
    def time_init(cls):
        cls.started_on = datetime.now()

    @classmethod
    def logger_init(cls):

        cls.out_log = logging.getLogger('stdout_logger')
        cls.out_log.setLevel(logging.INFO)
        stdout = logging.StreamHandler(sys.stdout)
        cls.out_log.addHandler(stdout)

        cls.str_log = logging.getLogger('string_logger')
        cls.str_log.setLevel(logging.INFO)
        cls.report = StringIO()
        string = logging.StreamHandler(cls.report)
        cls.str_log.addHandler(string)

        root_path = settings.BASE_DIR
        file_name = 'dedup-'+ cls.started_on.strftime('%Y-%m-%d') + '.log'
        cls.file_log = logging.getLogger('file_logger')
        cls.file_log.setLevel(logging.DEBUG)
        cls.log_file_path = path.join(root_path, file_name)
        file_log = logging.FileHandler(cls.log_file_path)
        cls.file_log.addHandler(file_log)

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

    @classmethod
    def log_comments_merge(cls, main_id, ids, comments):
        entry = cls.json_entry(main_id, ids, 'merge_comments', 'insert', 'sentence_id', comments)
        cls.file_log.info(entry)

    @classmethod
    @transaction.atomic
    def merge_comments(cls, main_id, ids):
        comments = list(SentenceComments.objects.filter(sentence_id__in=ids))
        for comment in comments:
            comment.id = None
            comment.sentence_id = main_id
        comments = SentenceComments.objects.bulk_create(comments)
        cls.log_comments_merge(main_id, ids, comments)

    @classmethod
    def log_logs_merge(cls, main_id, ids, logs):
        entry = cls.json_entry(main_id, ids, 'merge_logs', 'insert', 'sentence_id', logs)
        cls.file_log.info(entry)

    @classmethod
    @transaction.atomic
    def merge_logs(cls, main_id, ids):
        logs = list(Contributions.objects.filter(sentence_id__in=ids))
        for entry in logs:
            entry.id = None
            entry.sentence_id = main_id
        logs = Contributions.objects.bulk_create(logs)
        cls.log_logs_merge(main_id, ids, logs)

    @classmethod
    def log_tags_merge(cls, main_id, ids):
        tags = list(TagsSentences.objects.filter(sentence_id__in=ids))
        entry = cls.json_entry(main_id, ids, 'merge_tags', 'update', 'sentence_id', tags)
        cls.file_log.info(entry)
    
    @classmethod
    @transaction.atomic
    def merge_tags(cls, main_id, ids):
        cls.log_tags_merge(main_id, ids)
        TagsSentences.objects.filter(sentence_id__in=ids).update(sentence_id=main_id)

    @classmethod
    def log_lists_merge(cls, main_id, ids):
        links = list(SentencesSentencesLists.objects.filter(sentence_id__in=ids))
        entry = cls.json_entry(main_id, ids, 'merge_lists', 'update', 'sentence_id', links)
        cls.file_log.info(entry)

    @classmethod
    @transaction.atomic
    def merge_lists(cls, main_id, ids):
        cls.log_lists_merge(main_id, ids)
        SentencesSentencesLists.objects.filter(sentence_id__in=ids).update(sentence_id=main_id)

    @classmethod
    def log_favorites_merge(cls, main_id, ids):
        favs = list(FavoritesUsers.objects.filter(favorite_id__in=ids))
        entry = cls.json_entry(main_id, ids, 'merge_favorites', 'update', 'sentence_id', favs)
        cls.file_log.info(entry)

    @classmethod
    @transaction.atomic
    def merge_favorites(cls, main_id, ids):
        cls.log_favorites_merge(main_id, ids)
        FavoritesUsers.objects.filter(favorite_id__in=ids).update(favorite_id=main_id)

    @classmethod
    def log_annotations_merge(cls, main_id, ids):
        anns = list(SentenceAnnotations.objects.filter(sentence_id__in=ids))
        entry = cls.json_entry(main_id, ids, 'merge_annotations', 'update', 'sentence_id', anns)
        cls.file_log.info(entry)

    @classmethod
    @transaction.atomic
    def merge_annotations(cls, main_id, ids):
        cls.log_annotations_merge(main_id, ids)
        SentenceAnnotations.objects.filter(sentence_id__in=ids).update(sentence_id=main_id)

    @staticmethod
    def json_entry(main_id, ids, op, q, fld, objs):
        entry = {}
        entry['timestamp'] = datetime.utcnow().strftime('%Y-%m-%d %I:%M %p UTC')
        entry['operation'] = op
        entry['query'] = q
        entry['main_id'] = main_id
        entry['duplicate_ids'] = list(ids)
        entry['field_replaced'] = fld
        entry['rows_affected'] = serializers.serialize('json', objs)
        
        return json.dumps(entry)

    @classmethod
    def log_links_merge(cls, main_id, ids, replace='sent'):

        if replace == 'sent':
            sents = list(SentencesTranslations.objects.filter(sentence_id__in=ids))
            field = 'sentence_id'
        elif replace == 'tran':
            sents = list(SentencesTranslations.objects.filter(translation_id__in=ids))
            field = 'translation_id'

        entry = cls.json_entry(main_id, ids, 'merge_links', 'update', field, sents)
        cls.file_log.info(entry)
   
    @classmethod
    @transaction.atomic
    def merge_links(cls, main_id, ids):
        cls.log_links_merge(main_id, ids, 'sent')
        SentencesTranslations.objects.filter(sentence_id__in=ids).update(sentence_id=main_id)
        
        cls.log_links_merge(main_id, ids, 'tran')
        SentencesTranslations.objects.filter(translation_id__in=ids).update(translation_id=main_id)

    @classmethod
    def log_sents_del(cls, main_id, ids, sents):
        sents = list(sents)
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
        
        Contributions.objects.bulk_create(logs)

        entry = cls.json_entry(main_id, ids, 'delete Sentences', 'delete', 'sentence_id', sents)
        cls.file_log.info(entry)
    
    @classmethod
    @transaction.atomic
    def delete_sents(cls, main_id, ids):
        sents = Sentences.objects.filter(id__in=ids)
        cls.log_sents_del(main_id, ids, sents)
        sents.delete()

    @classmethod
    @transaction.atomic
    def deduplicate(cls, main_sent, ids):
        # merge
        cls.merge_comments(main_sent.id, ids)
        cls.merge_tags(main_sent.id, ids)
        cls.merge_links(main_sent.id, ids)
        cls.merge_lists(main_sent.id, ids)
        cls.merge_logs(main_sent.id, ids)
        cls.merge_favorites(main_sent.id, ids)
        cls.merge_annotations(main_sent.id, ids)
        
        # delete and log duplicates
        cls.delete_sents(main_sent.id, ids)
        
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
