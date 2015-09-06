#regex_replacer.py
# -*- coding: utf-8 -*-

#Script for replacing strings using regular expressions specified in a file

# See python_mysql_connector for the keywords that specify your MySQL credentials (user, password, db name, host).
# To run on the server, you must specify --user, --pwd, --db, --host.

#Note that you must download mysql.connector, since it doesn't come with the default distribution.
#Use: sudo apt-get update && sudo apt-get install python-mysql.connector
from __future__ import print_function
import codecs
import os
import re
import sys
from python_mysql_connector import PythonMySQLConnector

class RegexReplacer(PythonMySQLConnector):
    """
    Class for replacing text using a regex specified in a file.
    
    Only tested with Python 2.7.
    
    Reads a JSON file that contains the following elements:
    - a restrictor (possibly empty) for the SELECT statement
        - example: " AND lang='afr'"
    - a MySQL regex for matching an existing pattern
        - example: "[[:space:]]{2,}"
    - a Python regex for matching the same pattern
        - example: "\\s{2,}"
    - a substitution string to be fed to the re.sub() command
    to replace that pattern
        - example: " "
    - a flag that indicates whether the input string is ASCII (1=yes, 0=no)
        
    The fields may occur in arbitrary error, as they do in the 
    following examples.
    
    Contents of file for replacing "is" or its case variants by "**" 
    in Afrikaans sentences:
    [{"ascii": 1, "python_regex": "[iI][sS]", "mysql_regex": "[iI][sS]", "substitution_string": "**", "select_restrictor": " AND lang='afr'"}]

    Contents of file for replacing runs of whitespace with a single 
    space character in all sentences:
    [{"ascii": 1, "python_regex": "\\s{2,}", "mysql_regex": "[[:space:]]{2,}", "substitution_string": " ", "select_restrictor": ""}]

    Contents of file for replacing "à" (UTF-8 representation = 0xC3A0) 
    by "À" in Italian sentences: 
    [{"ascii": 0, "python_regex": "à", "mysql_regex": "C3A0", "substitution_string": "À", "select_restrictor": " AND lang='ita'"}]    
    """
    ASCII = "ascii"
    MYSQL_REGEX = "mysql_regex"
    PYTHON_REGEX = "python_regex"
    SELECT_RESTRICTOR = "select_restrictor"
    SUBSTITUTION_STRING = "substitution_string"
    def __init__(self):
        PythonMySQLConnector.__init__(self)
        self.json_fields = None

    def get_ascii(self):
        return self.json_fields[0][self.ASCII]
        
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
            epilog='Sample invocation: python regex_replacer.py --json_file config.json --user root --db tatoeba --pwd "" --csv_dir c:\\temp')
        self.parser.add_argument('--csv_basename', default='regex_replacer.csv', help='basename of intermediate csv file') 

    def process(self):
        filename = os.path.join(self.parsed.csv_dir, self.parsed.csv_basename)
        self.write_csv(filename)
        self.read_csv(filename)

    def read_csv(self, filename):
        """
        Read a CSV file (id<TAB>text) produced earlier and execute an SQL query to update text.
        """
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
        self.print_output("-------------------------------")
    
    def write_csv(self, filename):
        """
        Write a CSV file (id<TAB>text) containing IDs of sentences to be updated plus their new text.
        
        filename: the name to give the output file
        """
        cursor = self.cnx.cursor()
        mysql_regex_str = self.get_mysql_regex()
        mysql_regex_bytes = mysql_regex_str.encode('utf-8')
        ascii = self.get_ascii()
        if ascii:
            query = "SELECT id, text FROM sentences WHERE text regexp '{0}'{1};".format(
                mysql_regex_str, self.get_select_restrictor())
        else:    
            # Special processing is necessary when the search and/or replacement string contain/s 
            # non-ASCII characters. When this occurs:
            
            # (1) The JSON file must be encoded as UTF-8.
            
            # (2) The mysql_regex string in the JSON file must be written as a hex representation
            # of a UTF-8 string without any leading sequence of "0", "0x", "u", etc. For example,
            # lowercase Latin e grave (UTF-8 = 0xC3A8) would be written as "C3A8".
            
            # (3) The python_regex and substitution_string values must be written as literal strings
            # in UTF-8.
            
            # (4) The value of ascii in the JSON file must be set to 0.
            query = "SELECT id, text FROM sentences WHERE HEX(text) regexp '{0}'{1};".format(
                mysql_regex_bytes, self.get_select_restrictor())

        cursor.execute(query)
        python_regex_str = self.get_python_regex()
        python_regex_obj = re.compile(python_regex_str)
        out_f = codecs.open(filename, "w", "utf-8")
        substitution_str = self.get_substitution_string()
        sub_bytes = substitution_str.encode('utf-8')
        for (sid, text) in cursor:
            if ascii:
                new_text = python_regex_obj.sub(sub_bytes, text)
            else:
                new_text = text.decode("utf-8").replace(python_regex_str, substitution_str)
            # Apostrophes must be escaped.
            new_text = new_text.replace("'", r"\'")
            if ascii:
                line = "{0}\t{1}\n".format(sid, new_text).decode('utf-8')
            else:
                line = "{0}\t{1}\n".format(sid, new_text.encode('utf-8')).decode('utf-8')
            out_f.write(line)
        cursor.close()
        out_f.close()

    def __repr__(self):
        ret = "self.json_fields: {0}".format(self.json_fields)
        return ret
        
if __name__ == "__main__":
    replacer = RegexReplacer()
    replacer.process_args(sys.argv)
    replacer.set_log_file()
    replacer.read_json()
    replacer.connect()
    replacer.process()
    replacer.disconnect()
