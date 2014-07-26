IMOUTO
=========

IMOUTO is a collection of easy-to-use production and development server automation scripts for [tatoeba](http://tatoeba.org/eng/) website based on [vagrant](http://www.vagrantup.com/) and [ansible](http://www.ansible.com/home). It is divided into two major categories:

  - **imouto-devel:**  scripts for setting up and managing a development server
  - **imouto-prod:**  scripts for setting up and managing a production server (still in initial development phase)



Version
----

1.0


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
- If you wish to update the website code or configure sphinx later (once the vagrant up command has finished) you can do so using the following commands:
```bash
$ cd ansible
$ ansible-playbook -i ../.vagrant/provisioners/ansible/inventory/vagrant_ansible_inventory --private-key=~/.vagrant.d/insecure_private_key -u vagrant -U root update_code.yml
```
OR
```bash
$ cd ansible
$ ansible-playbook -i ../.vagrant/provisioners/ansible/inventory/vagrant_ansible_inventory --private-key=~/.vagrant.d/insecure_private_key -u vagrant -U root configure_sphinx.yml
```
You can copy `vagrant_ansible_inventory` and `insecure_private_key` to the ansible directory in order to avoid typing the full paths every time you run the above commands. Presently only these two roles are supported for individual provisioning. More roles will be added later.


Note:
- It takes a while for vagrant to download the ~300MB box on your machine and then to provision it using ansible. Please be patient and let it finish before running `vagrant ssh`.
- The current directory i.e. `admin/imouto-devel` on the host machine is synchronized with `/vagrant/` directory on the guest machine. So the code that is running the website can be directly accessed in `admin/imouto-devel/Tatoeba`. You can edit anything inside this directory and the changes will be automatically synced to the VM by vagrant.
- This project is still in its development stage, so there are high chances of bugs. Please report them through github's bug tracker.
- Currently the host port 8080 is forwarded to guest port 80. So you can check the website running on your host browser at "localhost:8080" once vagrant finishes provisioning the VM (i.e. after `vagrant up` finishes).
- This is only an initial set-up guide, I will write down a detailed usage guide at some point of time.

