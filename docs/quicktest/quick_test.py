#!/usr/bin/python

from bs4 import BeautifulSoup
from optparse import OptionParser, OptionGroup
import urllib
import re
import difflib

num = re.compile(r'\d+')
wrd = re.compile(r'[^\d\s]')
sent_num = re.compile(r'\?\d+')
tests = {}

def curl(url):
    socket = urllib.urlopen(url)
    html = socket.read()
    return html

def soupify(html):
    soup = BeautifulSoup(html)
    return soup

def strip_num(string):
    global num
    stripped = num.sub('', string)
    return stripped

def strip_wrd(string):
    global wrd
    stripped = wrd.sub('', string)
    return stripped

def strip_sent_num(html):
    html = num.sub('', html)
    return html

def load_file(name):
    f = open(name, "r")
    html = f.read()
    soup = soupify(html)
    f.close()
    return soup
    
def update_file(name, url):
    global tests
    fname = name + '.html'
    html = curl(url + tests[name])
    html = strip_sent_num(html)
    f = open(fname, "w")
    f.write(html)
    f.close()

def print_diff(diff):
    for line in diff:
        print line

options = OptionParser(usage='%prog [options]', description='Runs tests against a running instance of tatoeba.')
options.add_option('-p', '--port', type='string', default='80', dest='port', help='TCP port to the webserver (default: 80)')
options.add_option('-H', '--host', type='string', default='http://127.0.0.1', dest='host', help='Host address to connect to.')
options.add_option('-a', '--add-update', type='string', dest='update', default='', help='Force the given test name to update its html')
group = OptionGroup(options, 'Diff options', "Controls the format the diff is displayed in when a test fails. Use one at a time.")
group.add_option('-n', '--n-diff', action='store_const', const='n', dest='diff', help='Output diff in ndiff format.')
group.add_option('-c', '--context-diff', action='store_const', const='c', dest='diff', help='Output diff in context diff format.')
group.add_option('-u', '--unified-diff', action='store_const', const='u', dest='diff', default='u', help='Output diff in unified diff format.')
options.add_option_group(group)

testing_list = [
    (
        'root',
        '/',
        "soup.select('#annexe_content p')[1].string = " + \
        "strip_num(soup.select('#annexe_content p')[1].text)\n" + \
        "soup.select('#main_content .module')[0].div.decompose()"
    ),
]

def main():
    opts, args = options.parse_args()
    
    if len(args) > 0:
        options.print_help()
        return
        
    if opts.update:
        global tests
        for test in testing_list:
            tests.update({test[0]: test[1]})
        if opts.update not in tests:
            print "Invalid test name. Available tests %s" % (tests)
            return
        else:
            update_file(opts.update, opts.host + ':' + opts.port)
            return
    
    
    for test in testing_list:
        print 'Running Test: ' + test[0]
        soup_orig = load_file(test[0] + '.html')
        soup = soup_orig
        exec test[2]
        soup_orig = soup
        soup_orig = soup_orig.prettify()

        html = curl(opts.host + ':' + opts.port + test[1])
        html = strip_sent_num(html)
        soup_new = soupify(html)
        soup = soup_new
        exec test[2]
        soup_new = soup
        soup_new = soup_new.prettify()
        
        if soup_orig == soup_new:
            print 'TEST PASSED'       
        else:
            print 'TEST FAILED'
            print 'Dumping diff:'
            
        diff = ''
        soup_orig = soup_orig.splitlines()
        soup_new = soup_new.splitlines()
        
        if opts.diff == 'n':
            diff = difflib.ndiff(soup_orig, soup_new)
            print_diff(diff)            
        elif opts.diff == 'c':
            diff = difflib.context_diff(soup_orig, soup_new)
            print_diff(diff)
        elif opts.diff == 'u':
            diff = difflib.unified_diff(soup_orig, soup_new)
            print_diff(diff)

if __name__ == '__main__':
    main()
