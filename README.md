# Imouto

Imouto is a collection of automation scripts for building an instance of [Tatoeba](https://tatoeba.org/) using [ansible](http://www.ansible.com/home). This is the preferred way for developers to set up a local development environment.

## Requirements

Here are the basic requirements of the machine you’re using imouto from.

* A machine with a 64-bit CPU (32-bit should be possible too but we do not support it)
* GNU/Linux, MacOS or Windows
* Git (Windows users can use [Git for Windows](https://gitforwindows.org/))
* VirtualBox 4.0 or later (via package manager or [generic binaries](https://www.virtualbox.org/wiki/Downloads))
* Vagrant 1.7 or later (via package manager or [generic binaries](https://www.vagrantup.com/downloads.html))

## Installing a local instance

- Install the requirements above.

- Open a terminal. Windows users can run Git Bash (which comes with Git for Windows). In the terminal, clone Imouto’s repository and go into the cloned directory by running the following commands:

```bash
git clone https://github.com/Tatoeba/imouto
cd imouto
```

- If you need to use a proxy, follow the instructions in `README.proxy.md`.

- If you have less than 8GB of RAM, edit the file `Vagrantfile` to reduce value of `v.memory`, the amount of RAM allocated to the virtual machine. It is recommended that you allocate no more than 1/4 of your actual RAM. If you set `v.memory` to less than 2GB, the system will likely swap and run slow. As a workaround, [reduce the number of indexed languages](#limiting-the-number-of-indexed-languages) once the VM is running.

```
   v.memory = 1024 # only allocate 1GB of RAM to the VM
```

- Run this command to download and start up the Tatoeba VM. Please be patient, it takes a while.

```bash
vagrant up
```

- Once it completed, you should be able to access your local instance of Tatoeba at http://localhost:8080/

- You can log in using one of these accounts: `admin`, `corpus_maintainer`, `advanced_contributor`, `contributor`, `inactive` and `spammer`. For all of them the password is `123456`.

## Hacking Tatoeba

### Accessing the source code

The source code is inside the VM. While you can edit it using `vagrant ssh` and console editors, you might want to access the code directory from your machine so that you can edit it with your favorite editor or IDE. There are several ways to do this.

#### Using NFS (Unix/MacOS)

If you’re using GNU/Linux or MacOS, we recommend NFS because it’s fast and allows you to run `git` without noticeable delay. The source code is served over NFS by the VM. Add the following line to your /etc/fstab:

```
# Change /your/path/to/imouto/Tatoeba/ to the actual path of Imouto
localhost:/home/vagrant/Tatoeba /your/path/to/imouto/Tatoeba/ nfs noauto,user,exec,port=8049,soft,timeo=10
```

Now you should be able to run `mount Tatoeba/` and access the source code there.

#### Using Windows Shared Folder

If you’re using Windows, the source code is served as a Windows share. Open *Run* from the Start menu or the search, or by pressing Win+R. In the Run prompt, type `\\172.19.119.178\tatoeba`. This should open the source code of Tatoeba in the file explorer.

#### Using SSHFS (Unix/MacOS)

If for some reason the above options do not work for you, you can use the script `mount.sh`:

```bash
./mount.sh -M ./Tatoeba /home/vagrant/Tatoeba/
```

### Editing the source code

We recommend that you create your own fork of Tatoeba on GitHub. You will have to change the remote URL to point it to your fork:

```bash
# Change <username> with your Github username
git remote set-url origin git@github.com:<username>/tatoeba2.git
```

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

Development tools are all run from the command line. Windows users can run Git Bash (which comes with Git for Windows) while Unix and MacOS users must open a terminal. From there, `cd` to Imouto’s directory and run `vagrant ssh` to ssh into the VM. Then, run `cd Tatoeba` to enter the code. From there, you can execute development tools such as:

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

### Search engine

```bash
sudo systemctl start manticore # starts the search engine

sudo bin/cake sphinx_indexes # starts reindexation

sphinxql # runs the SphinxQL console

    sphinxQL> SELECT id FROM eng_main_index WHERE MATCH('hello');
    ...
```

### Generate exports

```bash
# This creates the files available on the Downloads page
sudo ./docs/cron/runner.sh ./docs/cron/export.sh
```

### Limiting the number of indexed languages

To limit RAM usage, you can limit which languages Manticore indexes using the following command. Re-run this command without `list-of-iso-codes` to make Manticore index all the languages again.

```bash
# Replace list-of-iso-codes with actual ISO codes separated by spaces.
# For example: lad eng spa por
cake sphinx_conf list-of-iso-codes | sudo tee /etc/manticoresearch/manticore.conf
sudo systemctl restart manticore
```

## Accessing subdomains (audio, downloads...)

To access subdomains, you need to configure them in the [hosts file](https://en.wikipedia.org/wiki/Hosts_%28file%29) of your machine (not the VM, your actual computer). Add the following line to your hosts file:

```
127.0.0.1 tato.test audio.tato.test downloads.tato.test
```

Now you should be able to access http://downloads.tato.test:8080/ as well as http://tato.test:8080/ (which is the same as localhost).
