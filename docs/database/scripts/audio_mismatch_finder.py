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
import sys
from python_mysql_connector import PythonMySQLConnector

class AudioMismatchFinder(PythonMySQLConnector):
    """Class for finding audio mismatches."""
    def __init__(self):
        PythonMySQLConnector.__init__(self)

    def init_parser(self):
        PythonMySQLConnector.init_parser(self)
        self.parser.add_argument('--base_mp3_dir', default='.',
            help='base directory where mp3 files are stored (e.g., "/home/tatoeba/audio/"')

    def process_args(self, argv):
        PythonMySQLConnector.process_args(self, argv)

    def process(self):
        stars = '*' * 35
        print("self.parsed.base_mp3_dir: " + "{0}".format(self.parsed.base_mp3_dir))
        lang_dirs = os.listdir(self.parsed.base_mp3_dir)
        for lang_dir in lang_dirs:
            if not os.path.isdir(os.path.join(self.parsed.base_mp3_dir, lang_dir)):
                print('not a dir: {0}'.format(lang_dir))
                continue
            print("{0}{1}{0}".format(stars, lang_dir))
            full_path = os.path.join(self.parsed.base_mp3_dir, lang_dir)
            files = glob.glob(os.path.join(full_path, '*.mp3'))
            basenames = frozenset([int(os.path.splitext(os.path.basename(file))[0]) for file in files])
            cursor = self.cnx.cursor()
            stmt = "SELECT id FROM sentences WHERE lang='{0}' and hasaudio='shtooka';".format(
                lang_dir)
            cursor.execute(stmt)
            sent_ids = frozenset([item[0] for item in cursor])
            missing_files = sorted(list(sent_ids - basenames))
            missing_ids = sorted(list(basenames - sent_ids))
            print("These sentences are marked as having audio, but do not have audio files:\n{0}".format(missing_files))
            print("These sentences have audio files, but are not marked as having audio:\n{0}".format(missing_ids))
        cursor.close()        

if __name__ == "__main__":
    user = 'root'

    # script_dir = os.path.dirname(os.path.realpath(__file__))

    finder = AudioMismatchFinder()
    finder.process_args(sys.argv)
    finder.connect()
    finder.set_log_file()
    finder.process()
    finder.disconnect()
