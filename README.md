IMOUTO
=========

IMOUTO is a collection of easy-to-use automation scripts for [tatoeba](http://tatoeba.org/eng/) website based on [vagrant](http://www.vagrantup.com/) and [ansible](http://www.ansible.com/home). It can be used to set up and provision both **development** and **production** servers.



Version
----

1.2


Usage Instructions
-----------

###imouto for development:
- Install `git` if not already present.
- Clone the github repo on your machine:
```bash
$ git clone https://github.com/Tatoeba/admin.git
```
- Install VirtualBox. You can download the appropriate package from [here](https://www.virtualbox.org/wiki/Linux_Downloads)
- Install vagrant 1.5 or later. You can download the package [here](https://www.vagrantup.com/downloads) or if you are on a debian based 64-bit machine, just run the following commands:
```bash
$ cd admin/imouto
$ sudo sh install_vagrant-1.6.3_x64.sh
```
- Install ansible 1.4 or later. You can follow the instructions [here](http://docs.ansible.com/intro_installation.html#getting-ansible) or simply use one of the following methods:
	- Method 1 (for Fedora, CentOS and RHEL users):
	```bash
	$ #install the epel-release RPM if needed on CentOS, RHEL, or Scientific Linux
	$ sudo yum install ansible
	```
	- Method 2 (for Ubuntu/Debian users):
	```bash
	$ sudo apt-add-repository ppa:rquillo/ansible
	$ sudo apt-get update
	$ sudo apt-get install ansible
	```
	- Method 3 (via python-pip):
	```bash
	$ sudo easy_install pip
	$ sudo pip install ansible
	```
- Use vagrant to first download the box and then provision it using ansible.
```bash
$ cd admin/imouto #ignore if already in the admin/imouto directory
$ vagrant up
```
- Run `vagrant ssh` to ssh to the machine.

####Post-provisioning tasks:
Once `vagrant up` has successfully finished, you will be able to SSH to the machine and check out the website at `localhost:8080` on the host. But you may also want to perform certain tasks independently without having to re-provision the whole machine again. To do that you can use the following command:
```bash
$ cd ansible
$ ansible-playbook -i ../.vagrant/provisioners/ansible/inventory/vagrant_ansible_inventory --private-key=~/.vagrant.d/insecure_private_key -u vagrant -U root playbook-name.yml
```
where `playbook-name.yml` is the name of the playbook that you want to run on the VM. Since the command is too long and very difficult to remember, you can use the following commands to create an alias:
```bash
$ echo "alias imouto-devel='ansible-playbook -i ../.vagrant/provisioners/ansible/inventory/vagrant_ansible_inventory --private-key=~/.vagrant.d/insecure_private_key -u vagrant -U root'" >> ~/.bashrc 
$ source ~/.bashrc
```
Now you can simply use the following command to run a playbook (inside the `ansible` directory):
```bash
$ imouto-devel playbook-name.yml
```

There are only 5 independent playbooks that are included with imouto currently:
- `update_code.yml`: To fetch the latest code from Tatoeba's github repository and update it on VM.
- `configure_sphinx.yml`: To configure sphinx search, create indexes and start the search daemon.
- `backup.yml`: To create a backup of the database, configurations and other static files of the VM on your machine.
- `restore.yml`: To restore the backup created using `backup.yml`.
- `restore_version.yml`: To restore to a particular revision of code that is available in `versions/` directory

#####Usage
The `restore.yml` playbook needs the name of the backup file to be restored, which can be specified in the group_vars/all file or through command line argument like this:
```bash
$ imouto-devel -e restore_src=path/to/backup/file.tar.gz restore.yml
```
The `restore_version` playbook needs the version number of the version to restore to. The versions are numbered from 1 to N where 1 is the latest version and N is oldest. The maximum value of N depends on the variable `revision_limit` that can be set in `group_vars/all`. If you enter an invalid value for `version`, the playbook will throw an error and tell you the possible valid values for it. The playbook can be run as follows:
```bash
$ imouto-devel -e version=3 restore_version.yml
```

#####Note:
- It takes a while for vagrant to download the ~300MB box on your machine and then to provision it using ansible. Please be patient and let it finish before running `vagrant ssh`.
- The current directory i.e. `admin/imouto` on the host machine is synchronized with `/vagrant/` directory on the guest machine. So the code that is running the website can be directly accessed in `admin/imouto/Tatoeba`. You can edit anything inside this directory and the changes will be automatically synced to the VM by vagrant.
- This project is still in its development stage, so there are high chances of bugs. Please report them through github's bug tracker.
- Currently the host port 8080 is forwarded to guest port 80. So you can check the website running on your host browser at "localhost:8080" once vagrant finishes provisioning the VM (i.e. after `vagrant up` finishes).
- This is only an initial set-up guide, I will write down a detailed usage guide at some point of time.

###imouto for production:
The same set of scripts of imouto (with a few changes) can be used for setting up production servers as well. You need to follow these steps:
- Install `git` if not already present.
- Clone the github repo on your machine (this should be different from the repo cloned for development server):
```bash
$ git clone https://github.com/Tatoeba/admin.git
```
- Install ansible 1.4 or later (see the **imouto for development** section for instructions).
- Edit `imouto/ansible/ansible.cfg`:
    * Uncomment the line `#ask_sudo_pass = True` if you want to enter sudo password through prompt
    * Uncomment the line `#ask_pass      = True` if you want to enter ssh password through prompt
- Edit `imouto/ansible/group_vars/all`:
    * Uncomment `ansible_ssh_user: tatoroot` and replace `tatoroot` with ssh username
    * Uncomment `ansible_ssh_port: 3022` and set the correct port number if you want to use a port other than `22` for ssh
- Edit `imouto/ansible/production-server` and replace `127.0.0.1` with the address of the server.
- Run ansible-playbook command to provision a playbook:
```bash
$ cd admin/imouto/ansible
$ ansible-playbook -i production-server playbook.yml
```
  To provision the whole server you can use `production.yml` playbook or to perform individual tasks you can use the other playbooks provided with imouto.
