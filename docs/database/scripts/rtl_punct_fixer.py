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

    def process_punct(self, punct_str):
        total_missing_files = 0
        if not (self.parsed.dry_run):
            print('Processing...')
        cursor = self.cnx.cursor(buffered=True)
        # To be modified for different initial punctuation.
        stmt = r"SELECT id, text FROM sentences WHERE lang = '{0}' and text REGEXP '^\\{1}[^{1}]';".format(
            self.parsed.lang, punct_str)

        #print('stmt: {0}'.format(stmt))
        cursor.execute(stmt)
        self.cnx.commit()    
        total_bad_sents = 0
        items = [i for i in cursor]
        for i in items:
            self.print_output("{0}:".format(i[0]))
            new_text = i[1][1:]
            new_text.append(punct_str)
            stmt = "UPDATE sentences SET text = '{0}' WHERE id = {1}".format(new_text, i[0])
            #print('stmt: {0}'.format(stmt))
            self.print_output("INITIAL: '{0}'\n  FINAL: '{1}'".format(i[1], new_text))
            if not (self.parsed.dry_run):
                cursor.execute(stmt)
                self.cnx.commit()    
        cursor.close()
        self.print_output("{0}SUMMARY{0}".format(RTLPunctFixer.stars))
        self.print_output("Total sentences for which '{0}' {1} moved: {2}".format(punct_str, 
                                                                                  'would have been' if self.parsed.dry_run else 'was', len(items)))
              
    def process(self):
        for punct_str in ('.', '?', '!'):
            self.process_punct(punct_str)

if __name__ == "__main__":
    user = 'root'

    # script_dir = os.path.dirname(os.path.realpath(__file__))

    fixer = RTLPunctFixer()
    fixer.process_args(sys.argv)
    fixer.connect()
    fixer.set_log_file()
    fixer.process()
    fixer.disconnect()
