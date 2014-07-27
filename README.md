IMOUTO
=========

IMOUTO is a collection of easy-to-use production and development server automation scripts for [tatoeba](http://tatoeba.org/eng/) website based on [vagrant](http://www.vagrantup.com/) and [ansible](http://www.ansible.com/home). It is divided into two major categories:

  - **imouto-devel:**  scripts for setting up and managing a development server
  - **imouto-prod:**  scripts for setting up and managing a production server (still in initial development phase)



Version
----

1.1


Usage Instructions
-----------

###imouto-devel:
- Install `git` if not already present.
- Clone the github repo on your machine:
```bash
$ git clone https://github.com/Tatoeba/admin.git
```
- Install VirtualBox. You can download the appropriate package from [here](https://www.virtualbox.org/wiki/Linux_Downloads)
- Install vagrant 1.5 or later. You can download the package [here](https://www.vagrantup.com/downloads) or if you are on a debian based 64-bit machine, just run the following commands:
```bash
$ cd admin/imouto-devel
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
$ cd admin/imouto-devel #ignore if already in the admin/imouto-devel directory
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
$ echo "alias imoutu-devel='ansible-playbook -i ../.vagrant/provisioners/ansible/inventory/vagrant_ansible_inventory --private-key=~/.vagrant.d/insecure_private_key -u vagrant -U root'" >> ~/.bashrc 
$ source ~/.bashrc
```
Now you can simply use the following command to run a playbook (inside the `ansible` directory):
```bash
$ imoutu-devel playbook-name.yml
```

There are only 4 independent playbooks that are included with imoutu currently:
- `update_code.yml`: To fetch the latest code from Tatoeba's github repository and update it on VM.
- `configure_sphinx.yml`: To configure sphinx search, create indexes and start the search daemon.
- `backup.yml`: To create a backup of the database, configurations and other static files of the VM on your machine.
- `restore.yml`: To restore the backup created using `backup.yml`.

The `restore.yml` playbook needs the name of the backup file to be resored, which can be specified in the group_vars/all file or through command line argument like this:
```bash
$ imoutu-devel -e restore_src=path/to/backup/file.tar.gz restore.yml
```


Note:
- It takes a while for vagrant to download the ~300MB box on your machine and then to provision it using ansible. Please be patient and let it finish before running `vagrant ssh`.
- The current directory i.e. `admin/imouto-devel` on the host machine is synchronized with `/vagrant/` directory on the guest machine. So the code that is running the website can be directly accessed in `admin/imouto-devel/Tatoeba`. You can edit anything inside this directory and the changes will be automatically synced to the VM by vagrant.
- This project is still in its development stage, so there are high chances of bugs. Please report them through github's bug tracker.
- Currently the host port 8080 is forwarded to guest port 80. So you can check the website running on your host browser at "localhost:8080" once vagrant finishes provisioning the VM (i.e. after `vagrant up` finishes).
- This is only an initial set-up guide, I will write down a detailed usage guide at some point of time.

