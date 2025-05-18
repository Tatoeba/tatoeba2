#!/usr/bin/env python3

from jinja2 import Template
import yaml
from os.path import isfile
from os import system, chdir
import sys

def get_ansible_vars():
    with open('ansible/host_vars/default') as ansible_conf:
        ansible_vars = yaml.load(ansible_conf, Loader=yaml.Loader)
    for var, value in ansible_vars.items():
        while True:
            prev = ansible_vars[var]
            ansible_vars[var] = Template(str(value)).render(ansible_vars)
            if prev == ansible_vars[var]:
                break
    return ansible_vars

def recursive_render(template, values):
     prev = template
     while True:
         current = Template(prev).render(**values)
         if current != prev:
             prev = current
         else:
             return current

def templatize(template, output, ansible_vars):
    with open(template) as template_f:
        result = recursive_render(template_f.read(), ansible_vars)
        with open(output, 'w') as app_local:
            app_local.write(result)

def generate_conf(ansible_vars):
    warning = None
    conf = 'config/app_local.php'
    output = conf
    template = output + '.template'

    avoid_overwrite = isfile(output)
    if avoid_overwrite:
        output = output + '.new'
        warning = f"""
Tatoeba warning: newly-generated {conf} installed
as {output}. Please check for any new settings to add into
your existing app_local.php.
"""

    templatize(template, output, ansible_vars)

    return warning

def setup():
    # Running "composer install" twice is a workaround
    # virtualbox bug https://www.virtualbox.org/ticket/18776.
    # See also https://github.com/laravel/homestead/issues/1240.
    system("composer install --no-progress --no-interaction --no-ansi --no-plugins --no-scripts")
    system("composer install --no-progress --no-interaction --no-ansi")
    system("bin/cake migrations migrate")
    system("bin/cake migrations migrate -p Queue")
    system("bin/cake languages_table reset")

def codeinit(argv):
    try:
        path = argv[1]
        chdir(path)
    except IndexError:
        pass

    ansible_vars = get_ansible_vars()
    warning = generate_conf(ansible_vars)
    setup()

    if warning:
        print(warning)

if __name__ == '__main__':
    codeinit(sys.argv)
