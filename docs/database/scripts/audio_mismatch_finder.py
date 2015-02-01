#audio_mismatch_finder.py

#Author: alanfgh

# Class for finding (1) sentences marked as having audio, but missing MP3 files and
# (2) sentences with audio files, but not marked as having audio.

# By default, uses the MySQL credentials (username, password, db name) and hostname of the VM.
# To run on the server, you must specify --username, --pwd, --db, --host, --port. See
# python_mysql_connector.py for more details on both these arguments and the 
# required mysql.connector package.

# Sample call on the VM, where MP3 files are located in /home/tatoeba/audiodir/eng,
# /home/tatoeba/audiodir/epo, etc.:
#     python audio_mismatch_finder.py --base_mp3_dir "/home/tatoeba/audiodir"

from __future__ import print_function
import argparse
import codecs
import mysql.connector
import glob
import os
import subprocess # for subprocess.call
import sys
import time # for sleep
from python_mysql_connector import PythonMySQLConnector

class AudioMismatchFinder(PythonMySQLConnector):
    """Class for finding audio mismatches."""
    stars = '*' * 35

    def __init__(self):
        PythonMySQLConnector.__init__(self)
        self.excluded_langs = set([])

    def init_parser(self):
        PythonMySQLConnector.init_parser(self)
        self.parser.add_argument('--base_mp3_dir', default='.',
            help='base directory where mp3 files are stored (e.g., "/home/tatoeba/audio/")')
        self.parser.add_argument('--lang', default='',
            help='3-letter code of language to check (default: check all languages)')
        self.parser.add_argument('--exclude_langs', default='',
            help='comma-separated string of 3-letter codes of languages to exclude (default: none)')
        self.parser.add_argument('--sleep', default=False, action='store_true', help='sleep between row fetches (prevents interface errors)')
        self.parser.add_argument('--db_is_truth', default=False, action='store_true', help='if true, "hasaudio=no" for sentence will cause mp3 file to be removed; otherwise, existing mp3 file will set "hasaudio=shtooka" for sentence')
        self.parser.add_argument('--archive_mp3_dir', default='./archived',
            help='base directory where (possibly bad) mp3 files are archived (e.g., "/home/tatoeba/audio/archived")')

    def make_archive_dir(self, lang_dir):
        archive_dir = os.path.join(self.parsed.archive_mp3_dir, lang_dir)
        if not os.path.isdir(archive_dir):
            os.makedirs(archive_dir)
        return archive_dir

    def sentence_has_mp3(self, id, lang):
        return os.isfile(self.path_to_audio_file(id, lang))

    def path_to_audio_file(self, id, lang):
        return os.path.join(self.parsed.base_mp3_dir, lang, "{0}.mp3".format(id))

    def path_to_archived_audio_file(self, id, lang):
        return os.path.join(self.parsed.archive_mp3_dir, lang, "{0}.mp3".format(id))

    def process_args(self, argv):
        PythonMySQLConnector.process_args(self, argv)
        self.excluded_lang_set = set(self.parsed.exclude_langs.split(','))

    def process_lang(self, lang_dir):
        print("{0}{1}{0}".format(AudioMismatchFinder.stars, lang_dir))
        full_path = os.path.join(self.parsed.base_mp3_dir, lang_dir)
        files = glob.glob(os.path.join(full_path, '*.mp3'))
        basenames = frozenset([int(os.path.splitext(os.path.basename(file))[0]) for file in files])
        cursor = self.cnx.cursor()
        stmt = "SELECT id FROM sentences WHERE lang='{0}' and hasaudio='shtooka';".format(
            lang_dir)
        cursor.execute(stmt)
        sent_w_audio_id_list = []
        count = 1
        if self.parsed.sleep:
            # List the sentences processed so far. Useful for debugging.
            for item in cursor:
                # print('item: {0}; count: {1}'.format(item[0], count))
                sent_w_audio_id_list.append(item[0])
                count += 1
                # The sleep() call is required to prevent this error from occurring 
                #     for some langs:
                # mysql.connector.errors.InterfaceError: 2013: Lost connection 
                #     to MySQL server during query
                time.sleep(0.000001) 
        else:
            sent_w_audio_id_list = [item[0] for item in cursor]
        sent_w_audio_ids = frozenset(sent_w_audio_id_list)
        missing_files = sorted(list(sent_w_audio_ids - basenames))
        missing_ids = sorted(list(basenames - sent_w_audio_ids))
        num_missing_files = len(missing_files)
        num_missing_ids = len(missing_ids)
        num_existing_sentences = 0
        num_skipped_sentences = 0
        if (num_missing_files > 0):
            print("These {0} sentences are marked as having audio, but do not have audio files:\n{1}".format(
                    num_missing_files, missing_files))
            for id in missing_files:
                stmt = "UPDATE sentences SET hasaudio = 'no' WHERE id = '{0}';".format(id)  
                print(stmt)
                if not self.parsed.dry_run:
                    cursor.execute(stmt)
        if (num_missing_ids > 0):
            # print("These {0} sentences (some of which may no longer exist) have audio files, but are not marked as having audio:\n{1}".format(
            #         num_missing_ids, missing_ids))
            existing_sentences = set([])
            skipped_sentences = {}
            for missing_id in missing_ids:
                stmt = "SELECT id FROM sentences WHERE id='{0}';".format(missing_id)
                cursor.execute(stmt)
                items = [item[0] for item in cursor]
                length = len(items)
                if (length > 0):
                    stmt = "SELECT lang FROM sentences WHERE id='{0}';".format(
                        missing_id)
                    cursor.execute(stmt)
                    sent_lang = cursor.next()[0]
                    if (sent_lang != lang_dir):
                        skipped_sentences[missing_id] = sent_lang
                        print("WARNING: skipping sentence {0}; lang ({1}) does not match dir ({2})".format(
                                missing_id, sent_lang, lang_dir))
                    else:
                        existing_sentences.add(missing_id)
                        if self.parsed.db_is_truth:
                            src_file = self.path_to_audio_file(sent_id, lang_dir)
                            archive_dir = self.make_archive_dir(lang_dir)
                            tgt_file = self.path_to_archived_audio_file(sent_id, lang_dir))
                            stmt = "mv {0} {1}".format(src_file, tgt_file)
                            if self.parsed.dry_run:
                                print("Statement would be: {0}".format(stmt))
                            else:
                                subprocess.check_call(stmt)
                        else:
                            stmt = "UPDATE sentences SET hasaudio = 'shtooka' WHERE id='{0}';".format(
                                missing_id)
                            if self.parsed.dry_run:
                                print("Statement would be: {0}".format(stmt))
                            else:
                                #print("Executing statement: {0}".format(stmt))
                                cursor.execute(stmt)
            num_existing_sentences = len(existing_sentences)
            num_skipped_sentences = len(skipped_sentences)
            if (num_existing_sentences > 0):
                str = ''
                if not (self.parsed.dry_run):
                    str = ' and were updated'
                print("Of those, the following {0} sentences still exist{1}:\n{2}".format(
                        num_existing_sentences, str, sorted(list(existing_sentences))))
            if (num_skipped_sentences > 0):
                skipped_sentence_ids = list(sorted(skipped_sentences))
                print("The following {0} sentences were skipped:\n{1}".format(
                        num_skipped_sentences, skipped_sentence_ids))
                print("Consider issuing these commands:")
                archive_dir = self.make_archive_dir(lang_dir)
                for sent_id in skipped_sentence_ids:
                    src_file = self.path_to_audio_file(sent_id, lang_dir)
                    tgt_file = os.path.join(self.parsed.base_mp3_dir,
                                                     skipped_sentences[sent_id],
                                                     '{0}.mp3'.format(sent_id))
                    archive_file = os.path.join(archive_dir,
                                                     '{0}.mp3'.format(sent_id))
                    if os.path.isfile(tgt_file):
                        src_size = os.path.getsize(src_file)
                        tgt_size = os.path.getsize(tgt_file)
                        if (src_size == tgt_size):
                            # Assume these are identical copies and remove the
                            # one that's in the wrong directory.
                            line = 'rm {0}'.format(src_file)
                            print(line)
                        else:
                            # Get rid of the one in the wrong directory (move it
                            # to the archive).
                            line = 'mv {0} {1}'.format(src_file, archive_file)
                            print(line)
                    else:
                        line = 'mv {0} {1}'.format(src_file, tgt_file)
                        print(line)
        self.cnx.commit()        
        cursor.close()
        return (num_missing_files, num_missing_ids, num_existing_sentences, num_skipped_sentences)
 
    def process(self):
        total_missing_files = 0
        total_missing_ids = 0
        total_existing_sentences = 0
        total_skipped_sentences = 0
        if (self.parsed.lang == ""):
            lang_dirs = os.listdir(self.parsed.base_mp3_dir)
        else:
            lang_dirs = (self.parsed.lang, )    
        for lang_dir in lang_dirs:
            if lang_dir in self.excluded_lang_set:
                print('Excluded lang: {0}'.format(lang_dir))
                continue
            if os.path.isdir(os.path.join(self.parsed.base_mp3_dir, lang_dir)):
                (num_missing_files, num_missing_ids, num_existing_sentences, num_skipped_sentences) = self.process_lang(lang_dir)
                total_missing_files += num_missing_files
                total_missing_ids += num_missing_ids
                total_existing_sentences += num_existing_sentences
                total_skipped_sentences += num_skipped_sentences
            else:
                print('not a dir: {0}'.format(lang_dir))
                continue
        print("{0}SUMMARY{0}".format(AudioMismatchFinder.stars))
        print("Total sentences missing audio files: {0}".format(total_missing_files))
        print("Total sentences (some of which may no longer exist) with audio files but not marked as having audio: {0}".format(total_missing_ids))
        print("Total sentences skipped due to language mismatch: {0}".format(total_skipped_sentences))
              
if __name__ == "__main__":
    user = 'root'

    # script_dir = os.path.dirname(os.path.realpath(__file__))

    finder = AudioMismatchFinder()
    finder.process_args(sys.argv)
    finder.connect()
    finder.set_log_file()
    finder.process()
    finder.disconnect()
