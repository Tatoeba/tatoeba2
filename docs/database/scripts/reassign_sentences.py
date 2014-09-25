#reassign_sentences.py

#Author: alanf

#Class for reassigning sentences.

# By default, uses the MySQL credentials (username, password, db name) and hostname of the VM.
# To run on the server, you must specify --username, --pwd, --db, --host, --port. See
# python_mysql_connector.py for more details on both these arguments and the 
# required mysql.connector package.

# Sample call on the VM, where 100000 is the user id to whom the sentences are to be reassigned 
# and "/home/tatoeba/somedir/temp.txt" is a file, each line of which is a sentence id that is
# to be reassigned:
#     python reassign_sentences.py 100000 "/home/tatoeba/somedir/temp.txt"

import mysql.connector
import codecs
import os
import argparse
import sys
from python_mysql_connector import PythonMySQLConnector

class SentenceReassigner(PythonMySQLConnector):
    """Class for reassigning sentences to another user."""
    def __init__(self):
        PythonMySQLConnector.__init__(self)

    def init_parser(self):
        PythonMySQLConnector.init_parser(self)
        self.parser.add_argument('new_user_id', type=int, 
            help='id of user to whom sentences will be reassigned')
        self.parser.add_argument('sent_id_listfile',
            help='name of listfile; each line must be the id of a sentence to be reassigned')

    def process_args(self, argv):
        PythonMySQLConnector.process_args(self, argv)

    def process_file(self):
        #It would be more efficient to use prepared statements, but for some reason,
        #the apt-get command only wants to install a version of mysql.connector that
        #is too old to support it, so we use ordinary statements.
        self.cursor = self.cnx.cursor()
        sent_ids = [line.strip() for line in open(self.parsed.sent_id_listfile, "r")]
        for sent_id in sent_ids:
            stmt = "UPDATE sentences SET user_id = {0} WHERE id = {1}".format(
                self.parsed.new_user_id, sent_id)
            if self.parsed.dry_run:
                self.print_output(stmt)
            else:
                self.cursor.execute(stmt)

if __name__ == "__main__":
    user = 'root'

    # script_dir = os.path.dirname(os.path.realpath(__file__))

    reassigner = SentenceReassigner()
    reassigner.process_args(sys.argv)
    reassigner.connect()
    reassigner.set_log_file()
    reassigner.process_file()
    reassigner.disconnect()
