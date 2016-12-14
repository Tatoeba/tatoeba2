#audio_mover.py

#Author: alanfgh

# Class for moving audio associated with one sentence to another.

# By default, uses the MySQL credentials (username, password, db name) and hostname of the VM.
# To run on the server, you must specify --username, --pwd, --db, --host, --port. See
# python_mysql_connector.py for more details on both these arguments and the 
# required mysql.connector package.

# Sample call on the VM, where MP3 files are located in /home/tatoeba/audiodir/eng,
# /home/tatoeba/audiodir/epo, etc.:
#     python audio_mover.py --base_mp3_dir "/home/tatoeba/audiodir"

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

class AudioMover(PythonMySQLConnector):
    """Class for moving audio from one sentence to another."""
    stars = '*' * 35

    def __init__(self):
        PythonMySQLConnector.__init__(self)

    def init_parser(self):
        PythonMySQLConnector.init_parser(self)
        self.parser.add_argument('--old_id', type=int, default=0,
                                 help='id of sentence with which audio is currently associated')
        self.parser.add_argument('--new_id', type=int, default=0,
                                 help='id of sentence to which audio is to be moved')
        self.parser.add_argument('--base_mp3_dir', default='.',
            help='base directory where mp3 files are stored (e.g., "/home/tatoeba/audio/")')
        self.parser.add_argument('--old_lang', default='',
            help='3-letter code of language with which audio is currently associated')
        self.parser.add_argument('--new_lang', default='',
            help='3-letter code of language with which audio is to be associated (usually the same as old_lang')
        self.parser.add_argument('--archive_mp3_dir', default='./archived',
            help='base directory where (possibly bad) mp3 files are archived (e.g., "/home/tatoeba/audio/archived")')

    def make_archive_dir(self, lang_dir):
        archive_dir = os.path.join(self.parsed.archive_mp3_dir, lang_dir)
        if not os.path.isdir(archive_dir):
            os.makedirs(archive_dir)
        return archive_dir

    def process_args(self, argv):
        PythonMySQLConnector.process_args(self, argv)

    def sentence_exists_in_db(self, id):
        cursor = self.cnx.cursor()
        stmt = "SELECT count(*) FROM sentences WHERE id='{0}';".format(
            self.parsed.old_id)
        cursor.execute(stmt)
        # Treat as a loop, even though there should be only one iteration.
        for count in cursor:
            if count > 0:
                return True
            else:
                return False

    def sentence_has_audio_in_db(self, id):
        cursor = self.cnx.cursor()
        stmt = "SELECT count(*) FROM audios WHERE sentence_id='{0}';".format(
            self.parsed.old_id)
        cursor.execute(stmt)
        # Treat as a loop, even though there should be only one iteration.
        for count in cursor:
            if count > 0:
                return True
            else:
                return False

    def sentence_has_mp3(self, id, lang):
        return os.path.isfile(self.path_to_audio_file(id, lang))

    def path_to_audio_file(self, id, lang):
        return os.path.join(self.parsed.base_mp3_dir, lang, "{0}.mp3".format(id))

    def path_to_archived_audio_file(self, id, lang):
        return os.path.join(self.parsed.archive_mp3_dir, lang, "{0}.mp3".format(id))

    def process(self):
        if not self.sentence_has_audio_in_db(self.parsed.old_id):
            raise Exception('Sentence {0} is not marked in db as existing or as having audio'.format(
                    self.parsed.old_id))
        if not self.sentence_has_mp3(self.parsed.old_id, self.parsed.old_lang):
            raise Exception('There is no audio file for sentence {0} (in language {1})'.format(
                    self.parsed.old_id, self.parsed.old_lang))
        if not self.sentence_exists_in_db(self.parsed.new_id):
            raise Exception('Sentence {0} is not marked in db as existing or as having audio'.format(
                self.parsed.new_id))
        old_file = self.path_to_audio_file(self.parsed.old_id, self.parsed.old_lang)
        new_file = self.path_to_audio_file(self.parsed.new_id, self.parsed.new_lang)
        if self.sentence_has_mp3(self.parsed.new_id, self.parsed.new_lang):
            # If the new sentence already has an audio file, archive it.
            cmd = "mv {0} {1}".format(new_file, self.make_archive_dir(self.parsed.new_lang))
            print("{0}".format(cmd))
            if not self.parsed.dry_run:
                os.rename(new_file, self.path_to_archived_audio_file(self.parsed.new_id, 
                                                                 self.parsed.new_lang))
        cmd = "mv {0} {1}".format(old_file, new_file)
        print(cmd)
        if not self.parsed.dry_run:
            os.rename(old_file, new_file)
        cursor = self.cnx.cursor()
        stmt = "UPDATE audios SET sentence_id = '{0}' WHERE sentence_id = '{1}';".format(self.parsed.new_id, self.parsed.old_id)
        print(stmt)
        if not self.parsed.dry_run:
            ret = cursor.execute(stmt)
            #print('ret: {0}'.format(ret))

 
              
if __name__ == "__main__":
    user = 'root'

    # script_dir = os.path.dirname(os.path.realpath(__file__))

    mover = AudioMover()
    mover.process_args(sys.argv)
    mover.connect()
    mover.set_log_file()
    mover.process()
    mover.disconnect()
