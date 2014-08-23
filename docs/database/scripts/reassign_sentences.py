#python_mysql_connector.py

#Author: alanf

#Class for reassigning sentences.

# By default, uses the MySQL credentials (username, password, db name) and hostname of the VM.
# To run on the server, you must specify --username, --pwd, --db, --host, --port. See
# python_mysql_connector.py for more details on both these arguments and the 
# required mysql.connector package.

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

    def process_file(self, new_user_id, filename):
        #It would be more efficient to use prepared statements, but for some reason,
        #the apt-get command only wants to install a version of mysql.connector that
        #is too old to support it, so we use ordinary statements.
        self.cursor = self.cnx.cursor()
        sent_ids = [line.strip() for line in open(filename, "r")]
        for sent_id in sent_ids:
            stmt = "UPDATE sentences SET user_id = {0} WHERE id = {1}".format(new_user_id, sent_id)
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
    # Sample call, where 1000 is the user id to whom the sentences are to be reassigned 
    # and "/home/tatoeba/somedir/temp.txt" is a file, each line of which is a sentence id
    # that is to be reassigned to user 100000.
    # reassigner.process_file(100000, "/home/tatoeba/somedir/temp.txt")
    reassigner.disconnect()
