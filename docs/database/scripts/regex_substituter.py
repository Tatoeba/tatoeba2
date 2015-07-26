#regex_substituter.py

#Script for replacing strings using regular expressions specified in a file

# See python_mysql_connector for the keywords that specify your MySQL credentials (user, password, db name, host).
# To run on the server, you must specify --user, --pwd, --db, --host.

#Note that you must download mysql.connector, since it doesn't come with the default distribution.
#Use: sudo apt-get update && sudo apt-get install python-mysql.connector
from __future__ import print_function
#import argparse
import codecs
#import json
#import mysql.connector
import os
import re
import sys
from python_mysql_connector import PythonMySQLConnector

class RegexSubstituter(PythonMySQLConnector):
    """
    Class for replacing text using a regex specified in a file.
    
    Reads a JSON file that contains a restrictor for the SELECT 
    statement (possibly empty), a MySQL regex,
    a Python regex, and a substitution string (to be fed to the re.sub() command). 
    
    Contents of sample file (for replacing runs of whitespace with a single space character):
    
    [{"python_regex": "\\s{2,}", "mysql_regex": "[[:space:]]{2,}", "substitution_string": " ", "select_restrictor": " AND lang='eng'"}]

    Contents of sample file (for replacing "is" by "IS" in Afrikaans sentences):
    [{"python_regex": "is", "mysql_regex": "is", "substitution_string": "IS", "select_restrictor": " AND lang='afr'"}]

    """
    MYSQL_REGEX = "mysql_regex"
    PYTHON_REGEX = "python_regex"
    SELECT_RESTRICTOR = "select_restrictor"
    SUBSTITUTION_STRING = "substitution_string"
    def __init__(self):
        PythonMySQLConnector.__init__(self)
        self.json_fields = None
        
    def get_mysql_regex(self):
        return self.json_fields[0][self.MYSQL_REGEX]
        
    def get_python_regex(self):
        return self.json_fields[0][self.PYTHON_REGEX]
        
    def get_select_restrictor(self):
        return self.json_fields[0][self.SELECT_RESTRICTOR]
        
    def get_substitution_string(self):
        return self.json_fields[0][self.SUBSTITUTION_STRING]
        
    def init_parser(self):
        PythonMySQLConnector.init_parser(self, description='Uses a regex to replace instances of a matching pattern',   
            epilog='Sample invocation: python regex_substituter.py --json_file config.json --user root --db tatoeba --pwd "" --csv_dir c:\\temp')
        self.parser.add_argument('--csv_basename', default='regex_substituter.csv', help='basename of intermediate csv file') 

    def process(self):
        filename = os.path.join(self.parsed.csv_dir, self.parsed.csv_basename)
        self.write_csv(filename)
        self.read_csv(filename)

    def read_csv(self, filename):
        """Read a CSV file (id<TAB>text) produced by an earlier step and execute an SQL query to update text."""
        self.print_output("\n\nfilename: {0}".format(filename))
        if self.parsed.dry_run:
            self.print_output("---NOT executing these lines---")
        in_f = codecs.open(filename, "r", "utf-8")
        cursor = self.cnx.cursor()
        for line in in_f:
            line_en = line.encode('utf-8')
            sid, text = line_en.split('\t')
            query = "UPDATE sentences SET text = '{0}' WHERE id = {1};".format(
                text.rstrip(), sid)
            self.print_output(query)
            if not self.parsed.dry_run:
                cursor.execute(query)
        if not self.parsed.dry_run:
            self.cnx.commit()
        cursor.close()
        in_f.close()
        self.print_output("--------------------------------")
    
    def write_csv(self, filename):
        """
        Write a CSV file (id<TAB>text) containing IDs of sentences to be updated plus their new text.
        
        filename: the name to give the output file
        
        mysql_regex: the regex to specify the input to be matched, in MySQL regex syntax
        
        py_regex: the regex to specify the input to be matched, in Python regex syntax
        
        select_restrictor: a string that is either blank or of the form " AND <some text>"
           where <some text> represents an additional qualifier for the SQL query.
           Example: " AND lang='eng'"
           
        substitution_str: a string in Python regex syntax
        """
        cursor = self.cnx.cursor()
        query = "SELECT id, text FROM sentences WHERE text regexp '{0}'{1};".format(
            self.get_mysql_regex(), self.get_select_restrictor())
        cursor.execute(query)
        regex = re.compile(self.get_python_regex())
        out_f = codecs.open(filename, "w", "utf-8")
        substitution_str = self.get_substitution_string()
        sub_bytes = substitution_str.encode('utf-8')
        for (sid, text) in cursor:
            new_text = regex.sub(sub_bytes, text)
            # Apostrophes must be escaped.
            new_text = new_text.replace("'", r"\'")
            line = "{0}\t{1}\n".format(sid, new_text)
            line_en = line.decode('utf-8')
            out_f.write(line_en)
        cursor.close()
        out_f.close()

    def __repr__(self):
        ret = "self.json_fields: {0}".format(self.json_fields)
        return ret
        
if __name__ == "__main__":
    substituter = RegexSubstituter()
    substituter.process_args(sys.argv)
    substituter.set_log_file()
    substituter.read_json()
    substituter.connect()
    substituter.process()
    substituter.disconnect()
