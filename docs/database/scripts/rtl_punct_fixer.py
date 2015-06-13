#rtl_punct_fixer.py

#Author: alanfgh

# Class for fixing sentences from right-to-left languages (Arabic, Hebrew) where the
# terminal punctuation is at the right rather than the left end of the sentence.

# By default, uses the MySQL credentials (username, password, db name) and hostname of the VM.
# To run on the server, you must specify --username, --pwd, --db, --host, --port. See
# python_mysql_connector.py for more details on both these arguments and the 
# required mysql.connector package.

# Sample call:
#     python rtl_punct_fixer.py  ###  --base_mp3_dir "/home/tatoeba/audiodir"

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

class RTLPunctFixer(PythonMySQLConnector):
    """Class for fixing terminal punctuation in sentences in right-to-left languages."""
    stars = '*' * 35

    def __init__(self):
        PythonMySQLConnector.__init__(self)
        self.excluded_langs = set([])

    def init_parser(self):
        PythonMySQLConnector.init_parser(self)
        self.parser.add_argument('--lang', default='',
            help='3-letter code of language to check')

    def process_args(self, argv):
        PythonMySQLConnector.process_args(self, argv)

    def process(self):
        total_missing_files = 0
        if not (self.parsed.dry_run):
            print('Processing...')
        cursor = self.cnx.cursor()
        # To be modified for different initial punctuation.
        stmt = "SELECT id FROM sentences WHERE lang='{0}' and text REGEXP '^\\\\...[^.];".format(
            self.parsed.lang)
        cursor.execute(stmt)
        self.cnx.commit()        
        cursor.close()
        total_bad_sents = 0
        for i in cursor:
            print("i: {0}".format(i))
        # print("{0}SUMMARY{0}".format(AudioMismatchFinder.stars))
        # print("Total sentences missing audio files: {0}".format(total_missing_files))
        # print("Total sentences (some of which may no longer exist) with audio files but not marked as having audio: {0}".format(total_missing_ids))
        # print("Total sentences skipped due to language mismatch: {0}".format(total_skipped_sentences))
              
if __name__ == "__main__":
    user = 'root'

    # script_dir = os.path.dirname(os.path.realpath(__file__))

    fixer = RTLPunctFixer()
    fixer.process_args(sys.argv)
    fixer.connect()
    fixer.set_log_file()
    fixer.process()
    fixer.disconnect()
