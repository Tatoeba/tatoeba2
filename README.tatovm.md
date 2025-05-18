# TatoVM

TatoVM is a ready-to-use virtual machine containing a web server, a database and other tools required to run [Tatoeba](https://tatoeba.org/). This is the preferred way for developers to set up a local development environment.

## Requirements

Here are the basic requirements of the machine you’re using TatoVM from.

* A machine with a 64-bit CPU (32-bit should be possible too but we do not support it)
* GNU/Linux, MacOS or Windows
* Git (Windows users can use [Git for Windows](https://gitforwindows.org/))
* VirtualBox 4.0 or later (via package manager or [generic binaries](https://www.virtualbox.org/wiki/Downloads))
* Vagrant 2.2.6 or later (via package manager or [generic binaries](https://www.vagrantup.com/downloads))

## Installing a local instance

- Install the requirements above. Note for Windows users: during the installation of Git for Windows, you will be asked to configure line endings conversions. Make sure you select "Checkout as-is, commit as-is". If you did not, it's possible to fix it later on by running `git config --global core.autocrlf false && git checkout .`.
  ![git_crlf](https://user-images.githubusercontent.com/5107734/116796220-29c0e800-aadb-11eb-9611-63e3afd8caa5.png)

- Fork the [tatoeba2 repository](https://github.com/Tatoeba/tatoeba2). If you already have a fork, make sure it is synched. Otherwise, clone a local copy of it and setup the upstream remote. If you are unsure about how to do any of this, refer to [this guide](https://docs.github.com/en/github/getting-started-with-github/fork-a-repo). Replace `octocat/Spoon-Knife` with `Tatoeba/tatoeba2`, and when the guide says "Open Terminal", Windows users can run Git Bash (which comes with Git for Windows).

- If you need to use a proxy, follow the instructions in [README.proxy.md](https://github.com/Tatoeba/tatoeba2/blob/dev/README.proxy.md).

- Make sure your current working directory is the location your fork. You should see a file called `Vagrantfile` in the current directory.

- Run this command to download and start up the Tatoeba VM. Please be patient, it takes a while.

```bash
vagrant up
```

- Once it completed, you should be able to access your local instance of Tatoeba at http://localhost:8080/

- You can log in using one of these accounts: `admin`, `corpus_maintainer`, `advanced_contributor`, `contributor`, `inactive` and `spammer`. For all of them the password is `123456`.

## Adding new sentences and finding them using the search

The default install contains no sentences at all and the search won’t work. You will have to [add a few sentences](http://localhost:8080/fra/sentences/add) on your own. Then:

- Perform a reindexation by following the instructions in the [Development tools](#development-tools) section.
- If the sentences you just added are the first sentences in that language, make sure you reindex the "delta" indexes first and then the "main" index (not the other way around).
- Once reindexation is complete, you should be able to find the sentences you just added using the search.

## Hacking Tatoeba

### Editing the source code

The source code is where you ran `vagrant up`, mostly under `src/`. Any change you do here should be reflected on http://localhost:8080/.

To Windows users: you may see unwanted changes when running `git diff`, such as:

```
$ git diff
diff --git a/bin/cake b/bin/cake
old mode 100755
new mode 100644
```

You can avoid this problem either by running `git config core.fileMode false`, or by creating a file named `.gitconfig` in the repository containing:

```
[core]
	fileMode = false
```

## Development tools

Development tools are all run from the command line. Windows users can run Git Bash (which comes with Git for Windows) while Unix and MacOS users must open a terminal. From there, `cd` to Tatoeba’s directory and run `vagrant ssh` to ssh into the VM. Then, run `cd Tatoeba` to enter the code. From there, you can execute development tools such as:

### Search

#### Reindex all the sentences

The search engine is configured to use a [main+delta](https://manual.manticoresearch.com/Creating_an_index/Local_indexes/Plain_index#Main+delta) schema. You can reindex all the "delta" and "main" indexes using the following commands respectively:

```sh
sudo bin/cake sphinx_indexes update delta
sudo bin/cake sphinx_indexes update main
```

To reindex the "main" or "delta" indexes of a particular language, add the ISO code, for example Bengali (ISO code `ben`):

```sh
sudo bin/cake sphinx_indexes update main ben
```


#### SphinxQL console

Run `sphinxql` to access the SphinxQL prompt. It will allow you to perform queries directly on the search engine, for example:

```sql
sphinxQL> SELECT id FROM eng_main_index WHERE MATCH('hello');
```

### Tests

```bash
phpunit # runs the whole test suite (takes a while)
phpunit tests/TestCase/Model/Table/SentencesTableTest.php # only a specific file
```

### Cake console

```bash
cake # gives help
cake migrations create MyNewMigration # creates a new migration
sudo -u www-data bin/cake queue runworker # execute queued jobs (background jobs)
```

### MySQL console

```bash
mysql tatoeba

    MariaDB [tatoeba]> SELECT * FROM users;
    ...

sudo mysql tatoeba # for operations requiring root privileges
```

### Generate exports

```bash
# Just run this command once
sudo ln -s /home/vagrant/Tatoeba /var/www-prod
# This creates the files available on the Downloads page
sudo ./docs/cron/runner.sh ./docs/cron/export.sh
```

## Accessing subdomains

To access subdomains, you need to configure them in the [hosts file](https://en.wikipedia.org/wiki/Hosts_%28file%29) of your machine (not the VM, your actual computer).

### Audio and downloads

Add the following line to your hosts file:

```
127.0.0.1 tato.test audio.tato.test downloads.tato.test
```

Now you should be able to access http://downloads.tato.test:8080/ as well as http://tato.test:8080/ (which is the same as localhost).

### Wiki

Add a line to your hosts file:

```
127.0.0.1 wiki.tato.test en.wiki.tato.test de.wiki.tato.test eo.wiki.tato.test es.wiki.tato.test
```

Now you should be able to access http://en.wiki.tato.test:8080/.

The above example only adds hostnames to access the English, German, Esperanto and Spanish wiki. If you want to add all the languages, here is a little command you can run from within the VM to extract all the languages from tatowiki's config file:

```sh
echo -n "127.0.0.1 wiki.tato.test"; \
  sudo sed '1,/"languages"/d;/ \],$/,$d' /srv/wiki.tatoeba.org/www/config.js | cut -d'"' -f2 | \
  while read lang; do echo -n " $lang.wiki.tato.test"; done; \
  echo
```

## Allocating more RAM to the VM

By default, 512 MB of your actual computer RAM are allocated to the VM. This should be enough to run a basic installation of Tatoeba. If you need more, edit the file `Vagrantfile` to change value of `v.memory`. For example:

```
   v.memory = 1024 # allocate 1GB of RAM to the VM
```
