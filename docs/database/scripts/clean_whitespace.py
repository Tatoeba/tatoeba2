#clean_whitespace.py

#Script for removing unwanted ASCII whitespace from sentences:
#  - leading/trailing whitespace
#  - internal sequences of more than one whitespace character
#  - internal tabs and newlines

# By default, uses the MySQL credentials (username, password, db name) and hostname of the VM.
# To run on the server, you must specify --username, --pwd, --db, --host.

#Note that you must download mysql.connector, since it doesn't come with the default distribution.
#Use: sudo apt-get update && sudo apt-get install python-mysql.connector
import mysql.connector
import codecs
import os
import argparse
import re

class WhitespaceCleaner:
    """Class for removing extra whitespace from text."""
    def __init__(self, parsed):
        self.parsed = parsed
        self.cnx = None
        self.log_filename = "log.txt"
        self.log_f = None

    def connect(self):
        self.cnx = mysql.connector.connect(user=self.parsed.user, 
                                      password=self.parsed.pwd, 
                                      host=self.parsed.host,
                                      port=self.parsed.port,
                                      database=self.parsed.db)

    def disconnect(self):
        self.cnx.close()

    def set_log_file(self, filename):
        self.log_filename = filename
        self.log_f = open(filename, "w") 

    def print_output(self, str):
        print str
        print >>self.log_f, str

    def read_csv(self, filename):
        """Read a CSV file (id<TAB>text) produced by an earlier step and execute an SQL query to update text."""
        self.print_output("\n\nfilename: {0}".format(filename))
        if self.parsed.dry_run:
            self.print_output("---NOT executing these lines---")
        in_f = codecs.open(filename, "r", "utf-8")
        cursor = self.cnx.cursor()
        for line in in_f:
            line_en = line.encode('utf-8')
            id, sep, text = line_en.partition('\t')
            query = "UPDATE sentences SET text = '{0}' WHERE id = {1};".format(text.rstrip(), id)
            self.print_output(query)
            if not self.parsed.dry_run:
                cursor.execute(query)
        cursor.close()
        in_f.close()
        self.print_output("--------------------------------")
    
    def write_csv_for_stripping_sents(self, filename):
        """Write a CSV file (id<TAB>text) containing IDs of sentences to be stripped of surrounding whitespace plus their new text."""
        cursor = self.cnx.cursor()
        cursor.execute("SELECT id, text FROM sentences WHERE text regexp '^[[:space:]]' OR text regexp '[[:space:]]$';")
        out_f = codecs.open(filename, "w", "utf-8")
        for (id, text) in cursor:
            new_text = text.strip()
            new_text = new_text.replace("'", r"\'")
            line = "{0}\t{1}\n".format(id, new_text)
            line_de = line.decode('utf-8')
            out_f.write(line_de)
        cursor.close()
        out_f.close()

    def write_csv_from_sents_w_regex(self, filename, mysql_regex, py_regex, substitution_str):
        """Write a CSV file (id<TAB>text) containing IDs of sentences to be updated plus their new text."""
        cursor = self.cnx.cursor()
        query = "SELECT id, text FROM sentences WHERE text regexp '{0}';".format(mysql_regex)
        cursor.execute(query)
        regex = re.compile(py_regex)
        out_f = codecs.open(filename, "w", "utf-8")
        for (id, text) in cursor:
            new_text = regex.sub(substitution_str, text)
            new_text = new_text.replace("'", r"\'")
            line = "{0}\t{1}\n".format(id, new_text)
            line_en = line.decode('utf-8')
            out_f.write(line_en)
        cursor.close()
        out_f.close()

if __name__ == "__main__":
    user = 'root'

    parser = argparse.ArgumentParser()
    parser.add_argument('--user', default='root', help='MySQL username')
    parser.add_argument('--pwd', default='tatoeba', help='MySQL password')
    parser.add_argument('--host', default='127.0.0.1', help='host (e.g., 127.0.0.1)')
    parser.add_argument('--port', default='3306', type=int, help='port (e.g., 3306)')
    parser.add_argument('--db', default='tatoeba', help='MySQL database')
    parser.add_argument('--dry_run', default=False, action='store_true', help='Use this to prevent execution')
    parser.add_argument('--csv_dir', default='', help='subdirectory to which csv files should be written') 
    parsed = parser.parse_args()

    # script_dir = os.path.dirname(os.path.realpath(__file__))

    cleaner = WhitespaceCleaner(parsed)
    cleaner.connect()

    log_filename = "log.txt"
    stripped_filename = "stripped.csv"
    space_seq_filename = "space_seq.csv"
    tab_filename = "tab.csv"
    newline_filename = "newline.csv"

    if parsed.csv_dir:
        if not os.path.exists(parsed.csv_dir):
            os.makedirs(parsed.csv_dir)
        log_filename = os.path.join(parsed.csv_dir, log_filename)
        stripped_filename = os.path.join(parsed.csv_dir, stripped_filename)
        space_seq_filename = os.path.join(parsed.csv_dir, space_seq_filename)
        tab_filename = os.path.join(parsed.csv_dir, tab_filename)
        newline_filename = os.path.join(parsed.csv_dir, newline_filename)

    cleaner.set_log_file(log_filename)
    
    # Strip leading/trailing whitespace.
    cleaner.write_csv_for_stripping_sents(stripped_filename)
    cleaner.read_csv(stripped_filename)

    # Use regex to find each sequence of ASCII whitespace and collapse it into an ordinary space. 
    # This block must be run before the blocks that follow it.
    cleaner.write_csv_from_sents_w_regex(space_seq_filename, "[[:space:]]{2,}", r"\s{2,}", " ")
    cleaner.read_csv(space_seq_filename)

    # Use regex to find each (remaining) ASCII tab and convert it into an ordinary space. 
    cleaner.write_csv_from_sents_w_regex(tab_filename, "[[.tab.]]", r"\t", " ")
    cleaner.read_csv(tab_filename)

    # Use regex to find each (remaining) ASCII newline and convert it into an ordinary space. 
    cleaner.write_csv_from_sents_w_regex(newline_filename, "[[.newline.]]", r"\n", " ")
    cleaner.read_csv(newline_filename)

    cleaner.disconnect()
