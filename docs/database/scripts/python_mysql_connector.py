#python_mysql_connector.py

#Author: alanf

#Base class for connecting to MySQL via Python, with defaults for Tatoeba.

# By default, uses the MySQL credentials (user, password, db name) and hostname of the VM.
# To run on the server, you must specify --user, --pwd, --db, --host, --port.

#Note that you must download mysql.connector, since it doesn't come with the default distribution.
#Use: sudo apt-get update && sudo apt-get install python-mysql.connector
from __future__ import print_function
import argparse
import codecs
import json
import mysql.connector
import os

class PythonMySQLConnector(object):
    """Base class for connecting to MySQL via Python, with defaults for Tatoeba."""
    def __init__(self):
        self.parser = None
        self.parsed = None
        self.cnx = None
        self.log_f = None
        self.json_fields = None
        self.init_parser()
        
    def init_parser(self, description='', epilog=''):
        self.parser = argparse.ArgumentParser(description=description, epilog=epilog)
        self.parser.add_argument('--user', default='root', help='MySQL username')
        self.parser.add_argument('--pwd', default='tatoeba', help='MySQL password')
        self.parser.add_argument('--host', default='localhost', help='host (e.g., 127.0.0.1)')
        self.parser.add_argument('--port', default='3306', type=int, help='port (e.g., 3306)')
        self.parser.add_argument('--db', default='tatoeba', help='MySQL database')
        self.parser.add_argument('--dry_run', default=False, action='store_true', help='Use this to prevent execution')
        self.parser.add_argument('--csv_dir', default='.', help='subdirectory to which csv files should be written') 
        self.parser.add_argument('--json_file', default='config.json', help='JSON file containing configuration') 
        self.parser.add_argument('--log_dir', default='.', help='subdirectory to which logs should be written') 
        self.parser.add_argument('--log_file', default='log.txt', help='logfile (without preceding path name)') 

    def process_args(self, argv):
        self.parsed = self.parser.parse_args(args=argv[1:])
        
    def connect(self):
        self.cnx = mysql.connector.connect(user=self.parsed.user, 
                                      password=self.parsed.pwd, 
                                      host=self.parsed.host,
                                      port=self.parsed.port,
                                      database=self.parsed.db)

    def disconnect(self):
        self.cnx.close()

    def read_json(self):
        try: 
            json_f = open(self.parsed.json_file, 'r')
            self.json_fields = json.load(json_f)
            json_f.close()
        except IOError as e:
            str = "Expected to find a JSON configuration file called {0}. Details:\n{1}".format(self.parsed.json_file, e)
            raise IOError(str)
        
    def write_json(self):
        json_f = json.open(self.parsed.json_file, 'w+')
        json.dump(self.json_fields, json_f)
        json_f.close()
        
    def set_log_file(self):
        if not os.path.exists(self.parsed.log_dir):
            os.makedirs(self.parsed.log_dir)
        log_filename = os.path.join(self.parsed.log_dir, self.parsed.log_file)
        self.log_f = open(log_filename, "w") 

    def print_output(self, str):
        print(str)
        print(str, file=self.log_f)

if __name__ == "__main__":
    user = 'root'

    # script_dir = os.path.dirname(os.path.realpath(__file__))

    connector = PythonMySQLConnector()
    connector.process_args(sys.argv)
    connector.set_log_file()
    connector.connect()
    connector.disconnect()
