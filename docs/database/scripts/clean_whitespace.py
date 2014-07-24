

#Note that you must download mysql.connector, since it doesn't come with the default distribution.
#Use: sudo apt-get update && sudo apt-get install python-mysql.connector
import mysql.connector
import codecs
import os
import argparse
import re

class TextUpdater:
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

    def read_csv(self, filename, execute=True):
        print "\n\nfilename: {0}".format(filename)
        if not execute:
            print "NOT executing these lines"
        in_f = codecs.open(filename, "r", "utf-8")
        cursor = self.cnx.cursor()
        for line in in_f:
            line_en = line.encode('utf-8')
            id, sep, text = line_en.partition('\t')
            query = "UPDATE sentences SET text = '{0}' WHERE id = {1};".format(text.rstrip(), id)
            print query
            if execute:
                cursor.execute(query)
        cursor.close()
        in_f.close()
        print "--------------------------------"
    
    def write_csv_for_stripping_sents(self, filename):
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
    parsed = parser.parse_args()

    # script_dir = os.path.dirname(os.path.realpath(__file__))

    updater = TextUpdater(parsed)
    updater.connect()

    filename = "stripped.csv"
    updater.write_csv_for_stripping_sents(filename)
    updater.read_csv(filename, execute=False)

    # This block must be run before the blocks that follow it.
    filename = "space_seq.csv"
    updater.write_csv_from_sents_w_regex(filename, "[[:space:]]{2,}", r"\s{2,}", " ")
    updater.read_csv(filename, execute=False)

    filename = "tab.csv"
    updater.write_csv_from_sents_w_regex(filename, "[[.tab.]]", r"\t", " ")
    updater.read_csv(filename, execute=False)

    filename = "newline.csv"
    updater.write_csv_from_sents_w_regex(filename, "[[.newline.]]", r"\n", " ")
    updater.read_csv(filename, execute=False)

    updater.disconnect()
