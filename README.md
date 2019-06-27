# Imouto

Imouto is a collection of automation scripts for building an instance of [Tatoeba](https://tatoeba.org/) using [ansible](http://www.ansible.com/home). This is the preferred way for developers to setup a local development environment.

## Requirements

Here are the basic requirements of the machine you’re using imouto from.

* A machine with a 64-bit CPU (32-bit should be possible too but we do not support it)
* GNU/Linux or MacOS
* Git
* VirtualBox 4.0 or later, which can be installed with a package manager or with the help of [generic binaries](https://www.virtualbox.org/wiki/Downloads))
* Vagrant 1.7 or later

## Installing a new instance

- Install the requirements above.

- Clone the repository and go to its directory:

```bash
git clone https://github.com/Tatoeba/imouto
cd imouto
```

- If you need to use a proxy, follow the instructions in `README.proxy.md`.

- If you have less than 8GB or RAM, edit the file `Vagrantfile` to reduce value of `v.memory`, the amount of RAM allocated to the virtual machine. It is recommended that you allocate no more than 1/4 of your actual RAM.

```
   v.memory = 1024 # only allocate 1GB of RAM to the VM
```

- Run this command to download and start up the Tatoeba VM. Please be patient, it takes a while.

```bash
vagrant up
```

- Once it completed, you should be able to access your local instance of Tatoeba at http://localhost:8080/

## Hacking Tatoeba

### Accessing the source code

The source code is inside the VM. While can edit it using `vagrant ssh` and console editors, you might want to mount the code directory from your machine so that you can edit it with your favorite editor or IDE. There are several ways to do this.

#### Using NFS (Unix/MacOS)

We recommend NFS because it’s fast and allows to run `git` without noticeable delay. The source code is served over NFS by the VM. Add the following line to your /etc/fstab:

```
# Change /your/path/to/imouto/Tatoeba/ to the actual path of Imouto
localhost:/home/vagrant/Tatoeba /your/path/to/imouto/Tatoeba/ nfs user,exec,port=8049,soft,timeo=10
```

Now you should be able to run `mount Tatoeba/` and access the source code there.

#### Using SSHFS

If for some reason NFS doesn’t work for you, you can use the script `mount.sh`:

```bash
./mount.sh -M ./Tatoeba /home/vagrant/Tatoeba/
```

### Editing the source code

- We recommend that you create your own fork of Tatoeba on Github. Because of this, you will have to change the remote URL to point it to your fork:

```bash
cd Tatoeba/
# Change <username> with your Github username
git remote set-url origin git@github.com:<username>/tatoeba2.git
```

## Develpment tools

Run `vagrant ssh` to ssh to the machine and `cd Tatoeba` to enter the code. From there, you can execute development tools such as:

### Running tests

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
