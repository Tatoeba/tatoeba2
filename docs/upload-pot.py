#!/usr/bin/python
# -*- coding: utf-8 -*-

from optparse import OptionParser
import os.path
import csv
from hashlib import md5
import ssl
import sys
import polib
import subprocess

from txclib import utils
try:
    from json import loads as parse_json, dumps as compile_json
except ImportError:
    from simplejson import loads as parse_json, dumps as compile_json
from txclib.project import Project
from txclib.log import set_log_level, logger
from txclib.packages.urllib3.exceptions import SSLError
from txclib.packages import urllib3
from txclib.urls import API_URLS
from txclib.web import user_agent_identifier, certs_file

# In python 3 default encoding is utf-8
if sys.version_info < (3,0):
    reload(sys) # WTF? Otherwise setdefaultencoding doesn't work
    # When we open file with f = codecs.open we specifi FROM what encoding to read
    # This sets the encoding for the strings which are created with f.read()
    sys.setdefaultencoding('utf-8')

class PotUploader(Project):
    def source_hash(self, string):
        context = ''
        keys = [string, context]
        return md5(':'.join(keys).encode('utf-8')).hexdigest()

    def make_request(self, method, url, body, content_type='application/json'):
        host = self.url_info['host']
        username = self.txrc.get(host, 'username')
        passwd = self.txrc.get(host, 'password')
        hostname = self.txrc.get(host, 'hostname')

        if host.lower().startswith('https://'):
            connection = urllib3.connection_from_url(
                host,
                cert_reqs=ssl.CERT_REQUIRED,
                ca_certs=certs_file()
            )
        else:
            connection = urllib3.connection_from_url(host)
        headers = urllib3.util.make_headers(
            basic_auth='{0}:{1}'.format(username, passwd),
            accept_encoding=True,
            user_agent=user_agent_identifier(),
            keep_alive=True
        )
        headers['content-type'] = content_type
        r = None
        try:
            r = connection.urlopen(method, url, headers=headers, body=body)
            if r.status < 200 or r.status >= 400:
                if r.status == 404:
                    raise utils.HttpNotFound(r.data)
                else:
                    raise Exception(r.data)
            return r.data
        except SSLError:
            logger.error("Invalid SSL certificate")
            raise
        finally:
            if not r is None:
                r.close()

    # raises as StopIteration if not found
    def find_by_msgid(self, po, msgid):
        return next(e for e in po if e.msgid == msgid)

    def fmt_update(self, old, new):
        return " • {}\n ↳ {}\n".format(old, new)

    def print_changes_summary(self, what, data):
        if len(data) == 0:
            logger.info("No strings {}".format(what))
        else:
            logger.info("Strings {}: {}".format(what, len(data)))
            for row in data:
                if type(row) is dict:
                    logger.info(self.fmt_update(row['old_msgid'], row['new'].msgid))
                else:
                    logger.debug("  {}".format(row.msgid))

    def sed_regex(self, regex, input):
        proc = subprocess.Popen(['sed', regex], stdout=subprocess.PIPE, stdin=subprocess.PIPE)
        proc.stdin.write(input)
        proc.stdin.close()
        output = proc.stdout.read()
        proc.wait()
        return output

    def compute_changes(self, oldpot, newpot, saver):
        deleted = []
        unchanged = []
        added = []
        updated = []
        for e in oldpot:
            try:
                new_e = self.find_by_msgid(newpot, e.msgid)
                unchanged.append(e)
            except StopIteration:
                if saver:
                    try:
                        regex, msgid = next(s for s in saver if s[1] == e.msgid)
                        edited_msgid = self.sed_regex(regex, msgid)
                        try:
                            new_e = self.find_by_msgid(newpot, edited_msgid)
                            updated.append({'old_msgid': e.msgid, 'new': new_e, 'regex': regex})
                        except StopIteration:
                            logger.info('Couldn\'t find new string “{}” by using regexp “{}” on string “{}”.'.format(edited_msgid, regex, msgid))
                            logger.debug('Try running:\necho "{}" | sed "{}"'.format(msgid, regex))
                            sys.exit(1)
                            pass
                    except StopIteration:
                        deleted.append(e)
                else:
                    deleted.append(e)

        for e in newpot:
            try:
                new_e = self.find_by_msgid(oldpot, e.msgid)
            except StopIteration:
                if not e.msgid in [u['new'].msgid for u in updated]:
                    added.append(e)

        self.print_changes_summary("unchanged", unchanged)
        self.print_changes_summary("deleted", deleted)
        self.print_changes_summary("added", added)
        self.print_changes_summary("updated", updated)
        return deleted, unchanged, added, updated

    def get_all_languages(self):
        response, charset = self.do_url_request('languages')
        response = parse_json(response)
        return [l['language_code'] for l in response]

    def update_updatable_strings(self, updated):
        logger.info('Processing updatable strings...')
        updated_strings = {}
        languages = self.get_all_languages()
        for language in languages:
            logger.debug("Language {}:".format(language))
            response, charset = self.do_url_request('strings', language=language)
            response = parse_json(response)
            updated_strings[language] = []
            for update in updated:
                try:
                    trans_string = next(r for r in response if r['key'] == update['old_msgid'])
                except StopIteration:
                    logger.warning("Couldn't find translation for “{}” in language {}.".format(update['old_msgid'], language))
                    continue
                original_trans = trans_string['translation']
                if len(original_trans) > 0:
                    updated_trans = self.sed_regex(update['regex'], original_trans)
                    new_trans = {
                        'source_entity_hash': self.source_hash(update['new'].msgid),
                        'translation': updated_trans,
                        'reviewed': False
                    }
                    if 'user' in trans_string:
                        new_trans['user'] = trans_string['user']
                    updated_strings[language].append(new_trans)
                    logger.debug(self.fmt_update(original_trans, updated_trans))
        return updated_strings

    def push_pot(self, pot_file):
        logger.info("Uploading new POT file ({})...".format(pot_file))
        self.do_url_request('resource_content', multipart=True, method="PUT",
                            files=[(
                                "%s;%s" % (self.url_info['resource'], 'en'),
                                pot_file
                            )])

    def push_strings(self, updated_strings):
        for language, request in updated_strings.iteritems():
            if len(request) == 0:
                continue

            logger.info("Uploading updated strings for language {}...".format(language))
            params = self.url_info
            params['language'] = language
            url = '/api/2/project/%(project)s/resource/%(resource)s/translation/%(language)s/strings' % params
            data = compile_json(request)
            self.make_request('PUT', url, data)

    def run(self, saver_file, dry_run, resource):
        API_URLS['resource_content'] = '/api/2/project/%(project)s/resource/%(resource)s/content/'
        API_URLS['strings'] = '/api/2/project/%(project)s/resource/%(resource)s/translation/%(language)s/strings?details'
        API_URLS['languages'] = '/api/2/project/%(project)s/languages/'

        if not resource:
            resource = self.get_resource_list()[0]
        try:
            project_slug, resource_slug = resource.split('.', 1)
        except ValueError:
            logger.error("Invalid resource name: {}".format(resource))
            sys.exit(1)
        self.url_info = {
            'host': self.get_resource_host(None),
            'project': project_slug,
            'resource': resource_slug
        }
        new_pot_file = self.get_source_file(resource)
        old_pot_file = "{}.old".format(new_pot_file)

        saver = self.load_saver(saver_file) if saver_file else None
        if os.path.isfile(old_pot_file):
            logger.info("Using already existing {} (delete it to force re-download)".format(old_pot_file))
        else:
            logger.info("Downloading resource file to {}...".format(old_pot_file))
            self.get_remote_pot(old_pot_file)
        oldpot = polib.pofile(old_pot_file)
        newpot = polib.pofile(new_pot_file)
        deleted, unchanged, added, updated = self.compute_changes(oldpot, newpot, saver)

        if updated:
            updated_strings = self.update_updatable_strings(updated)
        if not dry_run:
            self.push_pot(new_pot_file)
            if updated:
                self.push_strings(updated_strings)

    def load_saver(self, saver_file):
        trans_saves = []
        with open(saver_file, 'rb') as csvfile:
            lines = csv.reader(csvfile, delimiter="\t")
            for fields in lines:
                if (len(fields) > 0):
                    assert(len(fields) == 2)
                    trans_saves.append(fields)
        return trans_saves

    def get_remote_pot(self, pot_file):
        response, charset = self.do_url_request('resource_content')
        response = parse_json(response)

        with open(pot_file, "wb") as fd:
            fd.write(response['content'].encode("utf-8"))

def main(argv):
    usage = "usage: %prog [-n] [-r resource] [-s saves.csv]"
    description = "Uploads pot file to Transifex, and optionally tries"\
		" to save strings that would have been otherwise deleted"\
		" because Transifex removes all the 'fuzzy' strings (-s)."\
 	 	" It works by applying a sed command to strings that"\
 	 	" transforms a deleted string to a new one. The sed"\
                " command is applied to the translated strings in every"\
                " language, and the resulting strings are uploaded to"\
                " Transifex. The CSV file format is <sed command>\\t"\
                "<msgid>."

    parser = OptionParser(
        usage=usage, description=description
    )
    parser.disable_interspersed_args()
    parser.add_option(
        "-d", "--debug", action="store_true", dest="debug",
        default=False, help=("enable debug messages")
    )
    parser.add_option(
        "-q", "--quiet", action="store_true", dest="quiet",
        default=False, help="don't print status messages to stdout"
    )
    parser.add_option(
        "-r", "--resource", action="store", dest="resource", type="string",
        default=None, help="resource name (default is first found in .tx/config)"
    )
    parser.add_option(
        "-t", "--traceback", action="store_true", dest="trace", default=False,
        help="print full traceback on exceptions"
    )
    parser.add_option(
        "-s", "--saver", action="store", dest="saver_file", type="string",
        default=None, help="reupload deleted translations after POT upload using CSV file"
    )
    parser.add_option(
        "-n", "--dry-run", action="store_true", dest="dry_run",
        default=False, help="don't upload anything on Transifex"
    )
    (options, args) = parser.parse_args()

    utils.DISABLE_COLORS = True

    # set log level
    if options.quiet:
        set_log_level('WARNING')
    elif options.debug:
        set_log_level('DEBUG')
        import httplib
        httplib.HTTPConnection.debuglevel = 1

    path_to_tx = utils.find_dot_tx()

    potup = PotUploader(path_to_tx)
    try:
        potup.run(options.saver_file, options.dry_run, options.resource)
    except SSLError as e:
        sys.exit(1)
    except SystemExit:
        sys.exit()
    except:
        import traceback
        if options.trace:
            traceback.print_exc()
        else:
            formatted_lines = traceback.format_exc().splitlines()
            logger.error(formatted_lines[-1])
        sys.exit(1)

if __name__ == "__main__":
    main(sys.argv[1:])
