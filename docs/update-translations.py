#!/usr/bin/env python3


# Tool for updating UI translations for Tatoeba (tatoeba.org).
# Provides an automated way to retrieve human-readable po files 
# from the transifex site, compile them into binary mo files,
# and commit them to the main Git repository on GitHub.

section='https://www.transifex.com'
URL='https://www.transifex.com/api/2'

import os, requests, logging, datetime, subprocess, argparse, configparser
from getpass import getpass

config = configparser.ConfigParser()
configFilePath=os.path.expanduser('~')+'/.transifexrc'
if not os.path.isfile(configFilePath):
    open(configFilePath, 'a').close()

config.read(configFilePath)
if not config.has_section(section):
    config.add_section(section)
if not config.has_option(section, 'username'):
    username=input('Enter you Transifex\'s username: ')
    if username: config.set(section, 'username', username)
    else: quit()
if not config.has_option(section, 'password'):
    password=getpass('Enter you Transifex\'s password: ')
    if password: config.set(section, 'password', password)
    else: quit()

with open(configFilePath, 'w') as handle:
    config.write(handle)

config.read(configFilePath)

myTransifexUserName=config.get(section, 'username')
myTransifexPassword=config.get(section, 'password')
myAuthentication=(myTransifexUserName,myTransifexPassword)

languagesTable=[
          #['ab','abk'],          ['ca','cat'],          ['cs','ces'],          ['da','dan'],          ['ia','ina'],
          #['sv','swe'],          ['uz','uzb'],          ['xal','xal'],
          ['ar','ara'],          ['az','aze'],          ['be','bel'],          ['de','deu'],          ['el','ell'],
          ['en','eng'],          ['en_GB','en_GB'],     ['eo','epo'],          ['es','spa'],          ['et','est'],
          ['eu','eus'],          ['fi','fin'],          ['fr','fre'],          ['gl','glg'],          ['hi','hin'],
          ['hu','hun'],          ['it','ita'],          ['ja','jpn'],          ['jbo','jbo'],         ['ka','kat'],
          ['ko','kor'],          ['la','lat'],          ['lt','lit'],          ['mr','mar'],          ['ms','msa'],
          ['nds','nds'],         ['nl','nld'],          ['oc','oci'],          ['pl','pol'],          ['pt_BR','pt_BR'],
          ['ru','rus'],          ['ro','ron'],          ['tl','tgl'],          ['tr','tur'],          ['uk','ukr'],
          ['vi','vie'],          ['zh_CN','chi']
          ]


def printAndLog(textToLog):
    print(textToLog)
    logging.info(textToLog)

def executeCommand(commandToExecute, returnOrNot):
    p = subprocess.Popen(commandToExecute, stdout=subprocess.PIPE, shell=True)
    (output, err) = p.communicate()
    if returnOrNot:
        return output

def main():
    TMP_DIR=os.path.join('/','tmp','.fetch-translations-%s'%(datetime.datetime.now().strftime('%F-%T')))
    os.makedirs(TMP_DIR)
    LOG=os.path.join(TMP_DIR,'fetch.log')
    open(LOG, 'w').close()
    TRANSLATIONS_LOCAL=os.path.join(TMP_DIR,'tatoeba-transifex')
    MAIN_LOCAL=os.path.join(TMP_DIR,'tatoeba-github-git')
    logging.basicConfig(filename=LOG,filemode='a',format='%(asctime)s %(message)s',level=logging.DEBUG)
    COMMIT=False
    DONTCLEAN=False
    
    parser = argparse.ArgumentParser(description='''Script for updating UI translations for Tatoeba.
                                     Script assumes you have established SSH key access with GitHub. See:
                                     https://help.github.com/articles/generating-ssh-keys''')
    parser.add_argument('-c','--commit', help='Commit changes to GitHub repository.',
                        required=False, action='store_true')
    parser.add_argument('-n','--dontclean', help='Do not clean the temp directory.',
                        required=False, action='store_true')
    parser.add_argument('-i','--input', help='''Where to create the launchpad repository,
                        or the location of a local copy, if one already exists.''', required=False)
    parser.add_argument('-o','--output', help='''Where to create the main repository,
                        or the location of a local copy, if one already exists.
                        Please note that in this case, this path should
                        indicate the 'app/locale' directory of the repository''', required=False)
    args = parser.parse_args()
    if args.commit:
        COMMIT=True
    if args.dontclean:
        DONTCLEAN=True
    if args.input:
        TRANSLATIONS_LOCAL = args.input
    if args.output:
        MAIN_LOCAL = args.output
    
    if not COMMIT:
        print('Will not commit.')
    
    MAIN_ORIGIN="https://github.com/Tatoeba/tatoeba2.git"
    printAndLog('Initiating translations fetch - %s'%(TMP_DIR))
    if not os.path.exists(TRANSLATIONS_LOCAL):
        os.makedirs(TRANSLATIONS_LOCAL)
    printAndLog('Getting from Transifex')
    
    for language in languagesTable:
        if language[0]=='en': continue
        r=requests.get('%s/project/tatoeba_website/resource/tatoebaResource/translation/%s/?mode=default&file'%(URL,language[0]), auth=myAuthentication)
        if r.status_code != requests.codes.ok:
            if r.status_code==401:
                printAndLog('Please check your Transifex\'s user information in ~/.transifexrc.')
                quit()
            printAndLog('Problem with %s'%language)
            continue
        with open(os.path.join(TRANSLATIONS_LOCAL,'%s.po'%language[0]),'w') as handle:
            handle.write(r.text.encode("iso8859-1").decode('utf8'))
    
    if os.path.exists(MAIN_LOCAL):
        print('Pulling from the main git repository')
        logging.info(executeCommand('cd %s && git pull'%(MAIN_LOCAL)),True)
    else:
        os.makedirs(MAIN_LOCAL)
        print('Checkout from the main git repository')
        logging.info(executeCommand('git clone %s %s'%(MAIN_ORIGIN,MAIN_LOCAL),True))
    
    for language in languagesTable:
        languageFile=os.path.join(TRANSLATIONS_LOCAL,'%s.po'%language[0])
        if language[0]=='en': continue
        if not os.path.exists(languageFile):
            printAndLog('Problem with %s'%language)
            continue
        languagePath=os.path.join(MAIN_LOCAL,'app','locale',language[1],'LC_MESSAGES')
        if not os.path.exists(languagePath):
            os.makedirs(languagePath)
        printAndLog('Converting %s.po'%language[0])
        os.system('msgfmt "%s" -o "%s/default.mo"'%(languageFile,languagePath))
        os.system('cp "%s" "%s/%s.po"'%(languageFile,languagePath,language[0]))
    
    if executeCommand('cd %s && git status'%MAIN_LOCAL,True) == '':
        print('git status: nothing has changed. will not commit.')
    else:
        if COMMIT:
            printAndLog(executeCommand('cd %s && git commit -m "Translations updated via update-translations.py."'%MAIN_LOCAL,True))
            print('Changes have been committed.')
            printAndLog(executeCommand('cd %s && git push origin master'%MAIN_LOCAL,True))
            print('Changes have been pushed to master.')
        else:
            print('Changes haven\'t been committed. Use -c to commit.')
    
    if DONTCLEAN:
        print('Cleaning tmp directory: %s'%TMP_DIR)
        os.remove(TMP_DIR)
    print('Done.')

if __name__ == "__main__":
   main()
