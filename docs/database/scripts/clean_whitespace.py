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

    def connect(self):
        self.cnx = mysql.connector.connect(user=self.parsed.user, 
                                      password=self.parsed.pwd, 
                                      host=self.parsed.host,
                                      database=self.parsed.db)

    def disconnect(self):
        self.cnx.close()

    def read_csv(self, filename):
        """Read a CSV file (id tab text) produced by an earlier step and execute an SQL query to update text."""
        print "\n\nfilename: {0}".format(filename)
        if self.parsed.dry_run:
            print "---NOT executing these lines---"
        in_f = codecs.open(filename, "r", "utf-8")
        cursor = self.cnx.cursor()
        for line in in_f:
            line_en = line.encode('utf-8')
            id, sep, text = line_en.partition('\t')
            query = "UPDATE sentences SET text = '{0}' WHERE id = {1};".format(text.rstrip(), id)
            print query
            if not self.parsed.dry_run:
                cursor.execute(query)
        cursor.close()
        in_f.close()
        print "--------------------------------"
    
    def write_csv_for_stripping_sents(self, filename):
        """Write a CSV file (id tab text) containing IDs of sentences to be stripped of surrounding whitespace plus their new text."""
        cursor = self.cnx.cursor()
        cursor.execute("SELECT id, text FROM sentences WHERE text regexp '^[[:space:]]' OR text regexp '[[:space:]]$';")
        out_f = codecs.open(filename, "w", "utf-8")
        for (id, text) in cursor:
            new_text = text.strip()
            line = "{0}\t{1}\n".format(id, new_text)
            line_de = line.decode('utf-8')
            out_f.write(line_de)
        cursor.close()
        out_f.close()

    def write_csv_from_sents_w_regex(self, filename, mysql_regex, py_regex, substitution_str):
        """Write a CSV file (id tab text) containing IDs of sentences to be updated plus their new text."""
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
    parser.add_argument('--db', default='tatoeba', help='MySQL database')
    parser.add_argument('--dry_run', default=False, action='store_true', help='Use this to prevent execution')
    parsed = parser.parse_args()

    # script_dir = os.path.dirname(os.path.realpath(__file__))

    cleaner = WhitespaceCleaner(parsed)
    cleaner.connect()

    filename = "stripped.csv"
    cleaner.write_csv_for_stripping_sents(filename)
    cleaner.read_csv(filename)

    # This block must be run before the blocks that follow it.
    filename = "space_seq.csv"
    cleaner.write_csv_from_sents_w_regex(filename, "[[:space:]]{2,}", r"\s{2,}", " ")
    cleaner.read_csv(filename)

    filename = "tab.csv"
    cleaner.write_csv_from_sents_w_regex(filename, "[[.tab.]]", r"\t", " ")
    cleaner.read_csv(filename)

    filename = "newline.csv"
    cleaner.write_csv_from_sents_w_regex(filename, "[[.newline.]]", r"\n", " ")
    cleaner.read_csv(filename)

    cleaner.disconnect()
