#!/usr/bin/env python3

# Tool for updating UI translations for Tatoeba (tatoeba.org).
# Provides an automated way to retrieve human-readable po files 
# from the transifex site, compile them into binary mo files,
# and commit them to the main Git repository on GitHub.

section='https://www.transifex.com'
URL='https://www.transifex.com/api/2'

#Note that the "requests" module must be imported with "sudo pip-3.2 install requests".
import os, requests, logging, datetime, subprocess, argparse, configparser, shutil
from getpass import getpass

config = configparser.ConfigParser()
configFilePath=os.path.expanduser('~')+'/.transifexrc'
if not os.path.isfile(configFilePath):
    open(configFilePath, 'a').close()

config.read(configFilePath)
if not config.has_section(section):
    config.add_section(section)
if not config.has_option(section, 'username'):
    username=input('Enter your Transifex username: ')
    if username: config.set(section, 'username', username)
    else: quit()
if not config.has_option(section, 'password'):
    password=getpass('Enter your Transifex password: ')
    if password: config.set(section, 'password', password)
    else: quit()

with open(configFilePath, 'w') as handle:
    config.write(handle)

config.read(configFilePath)

myTransifexUserName=config.get(section, 'username')
myTransifexPassword=config.get(section, 'password')
myAuthentication=(myTransifexUserName,myTransifexPassword)

def getLanguagesTable(txConfigFile):
    langMap = []
    txconf = configparser.ConfigParser()
    txconf.read(txConfigFile)
    args = txconf.get('main', 'lang_map')
    for arg in args.replace(' ', '').split(','):
        threeLetters, twoLetters = arg.split(':')
        langMap.append([threeLetters, twoLetters])
    return langMap

def printAndLog(textToLog):
    print(textToLog)
    logging.info(textToLog)

def executeCommand(commandToExecute, returnOrNot):
    p = subprocess.Popen(commandToExecute, stdout=subprocess.PIPE, shell=True)
    (output, err) = p.communicate()
    if returnOrNot:
        return output

def getResourcesFiles(languagesTable, resourceSlug, resourceFilename, translationsLocal, mainLocal):
    for language in languagesTable:
        if language[0]=='en': continue
        r=requests.get(
            '%s/project/tatoeba_website/resource/%s/translation/%s/?mode=default&file'%(
                URL,resourceSlug,language[0]), auth=myAuthentication)
        if r.status_code != requests.codes.ok:
            if r.status_code==401:
                printAndLog('Please check your Transifex user information in ~/.transifexrc.')
                quit()
            printAndLog('Problem with %s'%language)
            continue
        with open(os.path.join(translationsLocal,'%s.po'%language[0]),'w') as handle:
            handle.write(r.text.encode("iso8859-1").decode('utf8'))
    
    for language in languagesTable:
        languageFile=os.path.join(translationsLocal,'%s.po'%language[0])
        if language[0]=='en': continue
        if not os.path.exists(languageFile):
            printAndLog('Problem with %s'%language)
            continue
        languagePath=os.path.join(mainLocal,'src','Locale',language[1])
        if not os.path.exists(languagePath):
            os.makedirs(languagePath)
        printAndLog('Copying %s.po (%s)'%(resourceFilename,language[1]))
        os.system('cp "%s" "%s/%s.po"'%(languageFile,languagePath,resourceFilename))
        printAndLog('Compiling to %s.mo (%s)'%(language[1],resourceFilename))
        os.system('msgfmt "%s" -o "%s/%s.mo"'%(languageFile,languagePath,resourceFilename))
        cacheFile = os.path.join(mainLocal, 'tmp', 'cache', 'persistent', 'myapp_cake_core_translations_'+resourceFilename+'_'+language[1])
        if os.path.exists(cacheFile):
           printAndLog('Removing cache file ' + cacheFile);
           os.remove(cacheFile)

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
    parser.add_argument('-b','--branch', help='Branch to which changes should be pushed.',
                        default='transifex')
    parser.add_argument('-c','--commit', help='Commit changes to GitHub repository.',
                        required=False, action='store_true')
    parser.add_argument('-n','--dontclean', help='Do not clean the temp directory.',
                        required=False, action='store_true')
    parser.add_argument('-i','--input', help='''Where to create the Transifex repository,
                        or the location of a local copy, if one already exists.''', required=False)
    parser.add_argument('-o','--output', help='''Where to create the main repository,
                        or the location of a local copy, if one already exists.
                        Please note that in this case, this path should
                        indicate the 'app/locale' directory of the repository''', required=False)
    args = parser.parse_args()
    if args.branch:
        BRANCH = args.branch
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
    if os.path.exists(MAIN_LOCAL):
        if COMMIT:
            print('Pulling from the main git repository')
            logging.info(executeCommand('cd %s && git pull'%(MAIN_LOCAL),True))
    else:
        os.makedirs(MAIN_LOCAL)
        print('Checkout from the main git repository')
        logging.info(executeCommand('git clone %s %s'%(MAIN_ORIGIN,MAIN_LOCAL),True))

    languagesTable = getLanguagesTable(MAIN_LOCAL + '/.tx/config')

    printAndLog('Initiating translations fetch - %s'%(TMP_DIR))
    if not os.path.exists(TRANSLATIONS_LOCAL):
        os.makedirs(TRANSLATIONS_LOCAL)
    printAndLog('Fetching from Transifex')
    
    getResourcesFiles(languagesTable, 'tatoebaResource', 'default', TRANSLATIONS_LOCAL, MAIN_LOCAL)
    getResourcesFiles(languagesTable, 'tatoeba-languages', 'languages', TRANSLATIONS_LOCAL, MAIN_LOCAL)
    getResourcesFiles(languagesTable, 'admin', 'admin', TRANSLATIONS_LOCAL, MAIN_LOCAL)
    getResourcesFiles(languagesTable, 'countries', 'countries', TRANSLATIONS_LOCAL, MAIN_LOCAL)
    
    if executeCommand('cd %s && git status'%MAIN_LOCAL,True) == '':
        print('git status: nothing has changed. will not commit.')
    else:
        if COMMIT:
            printAndLog(executeCommand(
                'cd %s && git commit -m "Translations updated via update-translations.py."'%MAIN_LOCAL,True))
            print('Changes have been committed.')
            printAndLog(executeCommand('cd %s && git push origin %s'%(MAIN_LOCAL,BRANCH),True))
            print('Changes have been pushed to %s.'%BRANCH)
        else:
            print('Changes haven\'t been committed. Use -c to commit.')
    
    if not DONTCLEAN:
        print('Cleaning tmp directory: %s'%TMP_DIR)
        shutil.rmtree(TMP_DIR)
    print('Done.')

if __name__ == "__main__":
   main()
