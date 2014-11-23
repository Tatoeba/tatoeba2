# Merges duplicate sentences in a database.
# See README.md for instructions on setting up the dependencies for this script and running it.

from django.core.management.base import BaseCommand
from django.conf import settings
from tatoeba2.models import Sentences, SentencesTranslations, Contributions, Users, Wall, SentenceComments, WallThreadsLastMessage
from collections import defaultdict
from datetime import datetime
from optparse import make_option
from django.db import transaction, IntegrityError
from StringIO import StringIO
from os import path
from django.core import serializers
from django.db.models.loading import get_model
from django.db.models import Q
from termcolor import colored
from hashlib import sha1
from pytz import UTC as utc
import time
import logging
import sys
import json
import re


def now():
    return datetime.utcnow().replace(tzinfo=utc)


class Dedup(object):

    @classmethod
    def time_init(cls):
        cls.started_on = now()

    @classmethod
    def logger_init(cls, root_path=''):

        cls.out_log = logging.getLogger('stdout_logger')
        cls.out_log.setLevel(logging.INFO)
        stdout = logging.StreamHandler(sys.stdout)
        cls.out_log.addHandler(stdout)

        cls.str_log = logging.getLogger('string_logger')
        cls.str_log.setLevel(logging.INFO)
        cls.report = StringIO()
        string = logging.StreamHandler(cls.report)
        cls.str_log.addHandler(string)

        root_path = root_path or settings.BASE_DIR
        file_name = 'dedup-'+ cls.started_on.strftime('%Y-%m-%dT%H:%M') + '.log'
        cls.file_log = logging.getLogger('file_logger')
        cls.file_log.setLevel(logging.DEBUG)
        cls.log_file_path = path.join(root_path, file_name)
        file_log = logging.FileHandler(cls.log_file_path)
        cls.file_log.addHandler(file_log)

    @staticmethod
    def tally(sents, old_tally=None):
        tally = defaultdict(set) if not old_tally else old_tally
        for sent in sents:
                tally[(sent[0], sent[1])].add(sent[2])

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
            if sent.correctness == -1:
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
    def log_entry(cls, main_id, ids, op, q, fld, objs):
        cls.json_entry(main_id, ids, op, q, fld, objs)
        cls.out_entry(main_id, ids, op)
        
    @classmethod
    def json_entry(cls, main_id, ids, op, q, fld, objs):
        entry = {}
        entry['timestamp'] = now().strftime('%Y-%m-%d %I:%M %p UTC')
        entry['operation'] = op
        entry['query'] = q
        entry['main_id'] = main_id
        entry['duplicate_ids'] = list(ids)
        entry['field_replaced'] = fld
        entry['rows_affected'] = serializers.serialize('json', objs)
        
        cls.file_log.info(json.dumps(entry))
    
    @classmethod
    def out_entry(cls, main_id, ids, op):
        entry = []
        entry.append(op)
        entry.append(str(ids))
        entry.append('into')
        entry.append(str(main_id))
        entry = ' '.join(entry)
        
        pat = {
            'merge': colored('MERGE', 'yellow', attrs=['bold']),
            'delete': colored('DELETE', 'yellow', attrs=['bold']),
            'into': colored('INTO', 'yellow', attrs=['bold']),
            'update': colored('UPDATE', 'yellow', attrs=['bold']),
            'log_deletion': colored('LOG DELETION', 'yellow', attrs=['bold']),                    
        }
        entry = cls.multi_replace(entry, pat)
        
        cls.out_log.debug(entry)

    @staticmethod
    def multi_replace(txt, pat):
        pat = dict((re.escape(k), v) for k, v in pat.iteritems())
        regex = re.compile(r'|'.join(pat.keys()))
        txt = regex.sub(lambda m: pat[re.escape(m.group(0))], txt)
        
        return txt

    @classmethod    
    def log_report(cls, msg):
        cls.str_log.info(msg)
        
        pat = {
            'Running': colored('Running', 'blue', attrs=['bold']),
            'OK': colored('OK', 'green', attrs=['bold']),
            'YES': colored('YES', 'green', attrs=['bold']),
            'NO': colored('NO', 'red', attrs=['bold']),
        }
        msg = cls.multi_replace(msg, pat)
        cls.out_log.info(msg)

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
                datetime=now(),
                type='sentence',
                user_id=cls.bot.id,
                ))

        if not cls.dry:
            Contributions.objects.bulk_create(logs)

        cls.log_entry(main_id, ids, 'delete Sentences', 'delete', 'sentence_id', sents)
        cls.log_entry(main_id, ids, 'log_deletion Contributions', 'insert', 'sentence_id', logs)
    
    @classmethod
    @transaction.atomic
    def delete_sents(cls, main_id, ids):
        sents = Sentences.objects.filter(id__in=ids)
        cls.log_sents_del(main_id, ids, sents)
        if not cls.dry:
            sents.delete()

    @classmethod
    def log_collision_del(cls, model, main_id, ids, fld, query):
        query = query.all() # calls clone() on the queryset, to get around python references
        deletes = list(query)
        cls.log_entry(main_id, ids, 'delete update collisions '+model, 'delete', fld, deletes)
    
    @classmethod
    def log_update_merge(cls, model, main_id, ids, fld='sentence_id'):
        updates = list(get_model('tatoeba2.'+model).objects.filter(**{fld+'__in': ids}))
        cls.log_entry(main_id, ids, 'merge '+model, 'update', fld, updates)

    @classmethod
    @transaction.atomic
    def update_merge(cls, model, main_id, ids, fld='sentence_id', all_unique=False):

        unique_together = get_model('tatoeba2.'+model)._meta.unique_together
        if not cls.dry:
            # handle unique fields
            if unique_together:
                unique_flds = list(unique_together[0])
                unique_flds.remove(fld)
                # filter out current rows into sets
                flds = [fld] + unique_flds
                dups = list(get_model('tatoeba2.'+model).objects.filter(**{fld+'__in': ids}))
                main = list(get_model('tatoeba2.'+model).objects.filter(**{fld: main_id}))
                dups = set(tuple(getattr(obj, fld) for fld in flds) for obj in dups)
                main = set(tuple(getattr(obj, fld) for fld in flds) for obj in main)

                # simulate the update on the dups row set and check if
                # there's collisions in the main row set
                collisions = set()
                for obj in dups:
                    updated_obj = (main_id,) + tuple(obj[1:])
                    if updated_obj in main:
                        collisions.add(obj)

                # handle the need for having all the ids not match
                # (think self-linked sentences), checks for existing
                # main_id, dup_id pairs in the main row set
                if all_unique:
                    for id in ids:
                        repeated_dups = (main_id,) + tuple(id for fld in unique_flds)
                        if repeated_dups in main: collisions.add(repeated_dups)

                # delete collisions before update runs, field names and tuple
                # contents are matched back to build an orm filter, the query
                # is then built by chaining all the built filters
                query = get_model('tatoeba2.'+model).objects.none() 
                for collision in collisions:
                    filters = {}
                    for idx, fld in enumerate(flds):
                        filters[fld] = collision[idx]
                    query = query | get_model('tatoeba2.'+model).objects.filter(**filters)
                cls.log_collision_del(model, main_id, ids, fld, query)
                query.delete()

            # issue update
            cls.log_update_merge(model, main_id, ids, fld)    
            get_model('tatoeba2.'+model).objects.filter(**{fld+'__in': ids}).update(**{fld:main_id})

    @classmethod
    def log_insert_merge(cls, model, main_id, ids, fld, inserts):
        cls.log_entry(main_id, ids, 'merge '+model, 'insert', fld, inserts)

    @classmethod
    @transaction.atomic
    def insert_merge(cls, model, main_id, ids, fld='sentence_id', q_filters=Q()):
        Model = get_model('tatoeba2.'+model)
        inserts = list(Model.objects.filter(**{fld+'__in': ids}).filter(q_filters))
        
        for ins in inserts:
            ins.id = None
            setattr(ins, fld, main_id)

        if not cls.dry:            
            inserts = Model.objects.bulk_create(inserts)

        cls.log_insert_merge(model, main_id, ids, fld, inserts)

    @classmethod
    @transaction.atomic
    def deduplicate(cls, main_sent, ids, post_cmnt=False, dry=False):
        cls.dry = dry

        # merge
        cls.insert_merge('SentenceComments', main_sent.id, ids)
        cls.update_merge('TagsSentences', main_sent.id, ids)
        cls.update_merge('SentencesTranslations', main_sent.id, ids, all_unique=True)
        cls.update_merge('SentencesTranslations', main_sent.id, ids, 'translation_id', all_unique=True)

        cls.update_merge('SentencesSentencesLists', main_sent.id, ids)
        cls.insert_merge('Contributions', main_sent.id, ids, q_filters=Q(type='sentence', action='update') | Q(type='link'))
        cls.update_merge('FavoritesUsers', main_sent.id, ids, 'favorite_id')
        cls.update_merge('SentenceAnnotations', main_sent.id, ids)
               
        # delete and log duplicates
        cls.delete_sents(main_sent.id, ids)
        
        # fix correctness if needed
        if cls.not_approved:
            if not cls.dry:
                main_sent.correctness = -1
                main_sent.save()
            cls.log_entry(main_sent.id, [], 'update Sentences', 'update', 'correctness', [main_sent])
        
        # post comment on duplicate sentences if needed
        if post_cmnt:
            comments = []
            for id in ids:
                comments.append(
                    SentenceComments(
                        sentence_id=id,
                        text='This sentence has been merged with #{0} because it was a duplicate of it. If there were any linked translations, tags or comments here, they have been moved to #{0}.'.format(main_sent.id),
                        user_id=cls.bot.id,
                        created=now(),
                        hidden=0,
                        )
                    )
            SentenceComments.objects.bulk_create(comments)

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
        make_option(
            '-s', '--verbose-stdout', action='store_true', dest='verbose_out',
            help='every single merging operation is dumped to stdout'
            ),
        make_option(
            '-l', '--log-path', action='store', type='string', dest='path',
            help='specify log directory. defaults to django project\'s root'
            ),
        make_option(
            '-w', '--wall-post', action='store_true', dest='wall',
            help='post report on the wall'
            ),
        make_option(
            '-c', '--comment-post', action='store_true', dest='cmnt',
            help='post a comment on each merged sentence'
            ),
        make_option(
            '-d', '--dry-run', action='store_true', dest='dry',
            help='logs operations without actually issuing any queries'
            ),
        make_option(
            '-u', '--url', action='store', type='string', dest='url',
            help='url root path pointing to log directory. used in the wall post'),
        )

    def handle(self, *args, **options):

        if options.get('chunks') and options.get('since'):
            print 'conflicting options...'
            return

        self.time_init()
        self.logger_init(options.get('path'))
        if options.get('verbose_out'): self.out_log.setLevel(logging.DEBUG)

        chunks = options.get('chunks') or 10
        since = options.get('since')

        bot_name = options.get('bot_name') or 'deduplication_bot'
        try:
            Dedup.bot = Users.objects.get(username=bot_name)
        except Users.DoesNotExist:
            Dedup.bot = Users.objects.create(
                username=bot_name, password='', email='bot@bots.com',
                since=now(), last_time_active=now().strftime('%Y%m%d'),
                level=1, is_public=1, send_notifications=0, group_id=1
                )

        pause_for = options.get('pause_for') or 0
        post_cmnt = bool(options.get('cmnt'))
        dry = bool(options.get('dry'))
        url = options.get('url') or 'http://downloads.tatoeba.org/'
        if url[-1] != '/': url += '/'

        self.all_dups = []
        self.all_mains = []
        self.all_audio = []

        # incremental vs full scan routes
        if since:
            self.log_report('Running incremental scan at '+self.started_on.strftime('%Y-%m-%d %I:%M %p UTC'))
            # parse date
            since = datetime(*[int(s) for s in since.split('-')]).replace(tzinfo=utc)
            # pull in rows from time range
            self.log_report('Running filter on sentences added since '+since.strftime('%Y-%m-%d %I:%M %p'))
            sents = list(Sentences.objects.filter(created__range=[since, now()]))
            sents = [(int(sha1(sent.text).hexdigest(), 16), sent.lang, sent.id) for sent in sents]
            self.log_report('OK filtered '+str(len(sents))+' sentences')
            
            # tally to eliminate premature duplicates
            sent_tally = self.tally(sents)
            del sents

            # filter out duplicates (could probably be done in 1 raw query...)
            self.log_report('Running filter on sentences to find duplicates')
            dup_set = []            
            for ids in sent_tally.itervalues():
                ids = list(ids)
                sents = list(Sentences.objects.filter(id__in=ids))            
                if len(sents) > 1:
                    dup_set.append(sents)
            self.log_report('OK '+str(len(dup_set))+' duplicate sets found')
            del sent_tally

            self.log_report('Running deduplication transactions on duplicate sets')
            # deduplicate
            for sents in dup_set:
                # determine main sentence based on priority rules
                main_sent = self.prioritize(sents)
                self.all_audio.extend(list(self.has_audio))
                self.all_mains.append(main_sent.id)
                # separate duplicates from main sentence
                sents.remove(main_sent)
                # filter out ids
                ids = [sent.id for sent in sents]
                self.all_dups.extend(ids)
                # run a deduplication transaction
                self.deduplicate(main_sent, ids, post_cmnt, dry)
                # handle rate limiting
                if pause_for: time.sleep(pause_for)

        else:
            self.log_report('Running full scan at '+self.started_on.strftime('%Y-%m-%d %I:%M %p UTC'))
            # pull in sentences from db in chunks
            self.log_report('Running full table scan in '+str(chunks)+' queries')
            total = Sentences.objects.order_by('-id')[0].id
            sent_tally = defaultdict(set)
            for rng in self.chunked_ranges(chunks, total):
                sents = list(Sentences.objects.filter(id__range=rng))
                sents = [(int(sha1(sent.text).hexdigest(), 16), sent.lang, sent.id) for sent in sents]
                self.log_report('Running duplicate filtering on sentence range: '+ str(rng))
                sent_tally = self.tally(sents, sent_tally)
                self.log_report('OK')
                del sents

            self.log_report('OK full table scan and filtering done '+str(len(sent_tally))+' duplicate sets found')

            self.log_report('Running deduplication step')
            # deduplicate
            for ids in sent_tally.itervalues():
                if len(ids) > 1:
                    # pull in needed rows
                    sents = list(Sentences.objects.filter(id__in=ids))
                    
                    main_sent = self.prioritize(sents)
                    self.all_audio.extend(list(self.has_audio))
                    self.all_mains.append(main_sent.id)

                    # separate duplicates from main sent
                    sents.remove(main_sent)
                    ids.remove(main_sent.id)
                    self.all_dups.extend(ids)
                    
                    # run a deduplication transaction
                    self.deduplicate(main_sent, ids, post_cmnt, dry)

                    # handle rate limit
                    if pause_for: time.sleep(pause_for)

        self.log_report('OK '+str(len(self.all_dups))+' sentences merged into '+str(len(self.all_mains))+' sentences')
        
        # verification step
        self.log_report('Running verification step')

        # all audio should exist
        self.log_report('All audio intact? ')
        self.ver_audio = Sentences.objects.filter(id__in=self.all_mains, hasaudio__in=['shtooka', 'from_users']).count() == len(self.all_audio)
        msg = 'YES' if self.ver_audio else 'NO'
        self.log_report(msg)

        # all dups should be gone
        self.log_report('All duplicates removed? ')
        self.ver_dups = Sentences.objects.filter(id__in=self.all_dups).count() == 0
        msg = 'YES' if self.ver_dups else 'NO'
        self.log_report(msg)

        # all mains should exist
        self.log_report('All merged sentences intact? ')
        self.ver_mains = Sentences.objects.filter(id__in=self.all_mains).count() == len(self.all_mains)
        msg = 'YES' if self.ver_mains else 'NO'
        self.log_report(msg)        

        # no links should refer to dups
        self.log_report('No links refer to deleted duplicates? ')
        self.ver_links = SentencesTranslations.objects.filter(sentence_id__in=self.all_dups).count() == 0 and SentencesTranslations.objects.filter(translation_id__in=self.all_dups).count() == 0
        msg = 'YES' if self.ver_links else 'NO'
        self.log_report(msg)
        
        self.log_report('Deduplication finished running successfully at '+now().strftime('%Y-%m-%d %I:%M %p UTC')+', see full log at:')
        self.log_report(url + path.split(self.log_file_path)[-1].replace(' ', '%20'))
        
        # post a wall report if needed
        if options.get('wall'):
            lft = Wall.objects.all().order_by('-rght')[0].rght + 1
            rght = lft + 1
            w = Wall(
                owner=self.bot.id,
                content=self.report.getvalue(),
                date=now(), modified=now(),
                title='', hidden=0,
                lft=lft, rght=rght
                )
            w.save()
            WallThreadsLastMessage(id=w.id, last_message_date=w.modified).save()
