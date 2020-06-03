#!/usr/bin/python
# -*- coding: utf-8 -*-

import os.path
import sys
import subprocess

from txclib import utils
from txclib.project import Project
try:
    from json import loads as parse_json
except ImportError:
    from simplejson import loads as parse_json

class UiLanguagesCheck(Project):
    def get_ui_langs(self):
        our_path = os.path.dirname(os.path.realpath(__file__))
        core_file = our_path + '/../app/Config/core.php'
        php_script = ('define("WWW_ROOT", "");'
                      'define("ROOT", "");'
                      'define("DS", "");'
                      'include("src/Lib/LanguagesLib.php");'
                      '$conf = include("config/app_local.php");'
                      'print json_encode(array_map('
                      '    function($v) {'
                      '        return \App\Lib\LanguagesLib::languageTag($v[0]);'
                      '    },'
                      '    $conf["UI"]["languages"]'
                      '));')
        php_cmd = "php -r '" + php_script + "'"
        proc = subprocess.Popen(php_cmd, shell=True, stdout=subprocess.PIPE)
        return parse_json(proc.stdout.read())

    def run(self, resource=None):
        if not resource:
            resource = self.get_resource_list()[0]
        try:
            project_slug, resource_slug = resource.split('.', 1)
        except ValueError:
            logger.error("Invalid resource name: {}".format(resource))
            sys.exit(1)

        lang_map = self.get_resource_lang_mapping(resource)
        ui_langs = self.get_ui_langs()

        self.url_info = {
            'host': self.get_resource_host(None),
            'project': project_slug,
            'resource': resource_slug
        }
        all_stats = self._get_stats_for_resource()

        stats_iter = sorted(all_stats.items(), key=lambda kv: int(kv[1]['completed'][:-1]))
        print("{:3s}      [{}]".format('lang', 'is included in app_local.php'))
        for tx_code, lang_stats in stats_iter:
            try:
                our_code = lang_map[tx_code]
            except KeyError:
                our_code = tx_code
            available = our_code in ui_langs
            print("{:3s}: {:>4s} [{}]".format(our_code, lang_stats['completed'], 'X' if available else ' '))

def main(argv):
    path_to_tx = utils.find_dot_tx()

    check = UiLanguagesCheck(path_to_tx)
    check.run()

if __name__ == "__main__":
    main(sys.argv[1:])
