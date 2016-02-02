from django.core.management.base import BaseCommand
from django.conf import settings
from django.db import connections
from optparse import make_option
import os


ALL = 1

export_config = [
        {
            'table': 'users',
            'fields_whitelist': ALL,
            'fields_blacklist': {'password': '"dc59e60a5353bf329d0c961185055226"', 'email': 'mail{c}@mail.com'},
            },
        {
            'table': 'contributions',
            'fields_whitelist': ALL,
            'fields_blacklist': {'ip': '"127.0.0.1"'},
            'append_updates': True
            },
        {'table': 'contributions_stats'},
        {'table': 'favorites_users'},
        {'table': 'languages'},
        {'table': 'last_contributions'},
        {'table': 'reindex_flags'},
        {'table': 'sentence_comments'},
        {'table': 'sentences'},
        {'table': 'sentences_lists'},
        {'table': 'sentences_sentences_lists'},
        {'table': 'sentences_translations'},
        {'table': 'sinogram_subglyphs'},
        {'table': 'sinograms'},
        {'table': 'tags'},
        {'table': 'tags_sentences'},
        {'table': 'transcriptions'},
        {'table': 'users_languages'},
        {'table': 'users_sentences'},
        {'table': 'wall'},
        {'table': 'wall_threads_last_message'},
        ]

default_db = settings.DATABASES['default']['NAME']
copy_db = settings.DATABASES['copy']['NAME']


class Export(object):
    tbl_qry = '''
        DROP TABLE IF EXISTS {copy_db}.{table};
        CREATE TABLE {copy_db}.{table} LIKE {default_db}.{table};
        '''
    fld_ins_qry = '''
        INSERT INTO {copy_db}.{table} ({flds})
        SELECT {flds}
            FROM {default_db}.{table};
        '''
    fld_ins_defs_qry = '''
        INSERT INTO {copy_db}.{table} ({flds}, {flds_b})
        SELECT {flds}, {defs}
            FROM {default_db}.{table};
        '''
    fld_ins_upd_qry = '''
        INSERT INTO {copy_db}.{table} ({flds})
        SELECT {flds}
            FROM {default_db}.{table}
            WHERE {ufld} > {ufld_val};
        '''
    fld_ins_upd_defs_qry = '''
        INSERT INTO {copy_db}.{table} ({flds}, {flds_b})
        SELECT {flds}, {defs}
            FROM {default_db}.{table}
            WHERE {ufld} > {ufld_val};
        '''

    ctr = 1
    defs_ctr_qry = '''
        SET @{ctr_var} = 0;
        '''
    defs_ctr_concat_qry = '''
        CONCAT('{p1}', @{ctr_var} := @{ctr_var} + 1, '{p2}')
        '''

    @classmethod
    def ctr_concat(cls, c_str):
        if not hasattr(cls, 'ctr_sets'): cls.ctr_var_qrys = []

        c_str = c_str.replace('{c}', '-')
        c_lst = c_str.split('-')
        ctr_var='ctr'+str(cls.ctr)
        cls.ctr_var_qrys.append(
                cls.defs_ctr_qry.format(ctr_var=ctr_var)
                )
        concat = cls.defs_ctr_concat_qry\
                    .format(ctr_var=ctr_var, p1=c_lst[0], p2=c_lst[1])

        cls.ctr += 1
        return concat

    @staticmethod
    def get_flds(tbl):
        cursor = connections['default'].cursor()
        cursor.execute('SHOW COLUMNS FROM {tbl};'.format(tbl=tbl))
        rows = cursor.fetchall()
        flds = map(lambda x: x[0], rows)
        return flds

    @staticmethod
    def check_updates(tbl, ufld='id'):
        cursor = connections['copy'].cursor()
        cursor.execute('SELECT {ufld} FROM {tbl} ORDER BY {ufld} DESC LIMIT 1;'.format(tbl=tbl, ufld=ufld))
        last = cursor.fetchone()[0]
        return last

    @staticmethod
    def get_tbls(db):
        cursor = connections['copy'].cursor()
        cursor.execute('SHOW TABLES IN {db};'.format(db=db))
        rows = cursor.fetchall()
        tbls = map(lambda x: x[0], rows)
        cursor.close()
        return tbls

    @classmethod
    def run_export(cls, dump_file, extra_args=''):
        cmd = 'mysqldump -u {user} -p{password} {extra}\
               --quick --single-transaction --no-autocommit --extended-insert --no-create-db {db} {tbls}'
        user = settings.DATABASES['default']['USER']
        password = settings.DATABASES['default']['PASSWORD']

        os.popen('touch ' + dump_file)
        if cls.dir_exp:
            os.popen(cmd.format(
                user=user,
                password=password,
                extra=extra_args,
                db=default_db,
                tbls=' '.join(cls.dir_exp)
                )+ ' >> ' + dump_file
                )
        os.popen(cmd.format(
            user=user,
            password=password,
            extra=extra_args,
            db=copy_db,
            tbls=' '.join(cls.get_tbls(copy_db))
            )+ ' >> ' + dump_file
            )


    @classmethod
    def build_query(cls, config):
        qry = []
        cls.curr_tbls = cls.get_tbls(copy_db)
        cls.dir_exp = []

        for c in config:
            tbl = c['table']
            blck = c.get('fields_blacklist') or {}
            to_ins = bool(blck)
            defs_ctr = {k:v for k, v in blck.items() if '{c}' in v}
            flds_b = [k for k in blck.keys()]
            defs_b = [cls.ctr_concat(v) if '{c}' in v else v for v in blck.values()]
            flds_w = c.get('fields_whitelist') or ALL
            to_cpy = bool(c.get('force_copy'))
            to_upd = bool(c.get('append_updates') and unicode(tbl) in cls.curr_tbls)

            if flds_w is ALL and not blck and not to_cpy:
                cls.dir_exp.append(tbl)
                continue

            if flds_w is ALL:
                flds_w = filter(lambda x: x not in flds_b, cls.get_flds(tbl))

            if not to_upd:
                qry.append(cls.tbl_qry.format(copy_db=copy_db, default_db=default_db, table=tbl))

            if to_cpy:
                flds_w = ', '.join(list(flds_w))
                qry.append(cls.fld_ins_qry.format(copy_db=copy_db, default_db=default_db, table=tbl, flds=flds_w))

            elif to_ins:

                if defs_ctr:
                    qry.append(' '.join(cls.ctr_var_qrys))

                flds_w = ', '.join(list(flds_w))
                defs_b = ', '.join(list(defs_b))
                flds_b = ', '.join(list(flds_b))

                if to_upd:
                    ufld = 'id'
                    ufld_val = cls.check_updates(tbl)
                    qry.append(cls.fld_ins_upd_defs_qry.format(copy_db=copy_db, default_db=default_db, flds=flds_w, flds_b=flds_b, defs=defs_b, table=tbl, ufld=ufld, ufld_val=ufld_val))
                else:
                    qry.append(cls.fld_ins_defs_qry.format(copy_db=copy_db, default_db=default_db, flds=flds_w, flds_b=flds_b, defs=defs_b, table=tbl))

        return ''.join(qry)


class Command(Export, BaseCommand):
    option_list = BaseCommand.option_list + (
        make_option(
            '-d', '--dump-query', action='store_true', dest='dry',
            help='dry run and dump query to stdout'
            ),
        make_option(
            '-r', '--run-export', action='store_true', dest='exp',
            help='run mysqldump command'
            ),
        make_option(
            '-p', '--export-path', action='store', type='string', dest='file',
            help='file path for the export'
            ),
        make_option(
            '-a', '--extra-args', action='store', type='string', dest='extra',
            help='pass in extra args mysqldb'
            ),
        )

    def handle(self, *args, **options):
        dry = options.get('dry')
        exp = options.get('exp')
        dump_file = options.get('file') or 'export.sql'
        extra_args = options.get('extra') or ''

        qry = self.build_query(export_config)

        if dry:
            print qry
        else:
            cursor = connections['copy'].cursor()
            cursor.execute(qry)
            cursor.close()

        if exp:
            self.run_export(dump_file, extra_args)

