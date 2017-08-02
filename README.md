# Imouto

Imouto is a collection of easy-to-use automation scripts for [tatoeba](http://tatoeba.org/eng/) website based on [vagrant](http://www.vagrantup.com/) and [ansible](http://www.ansible.com/home). It can be used to set up and provision both **development** and **production** servers.

## Requirements

These requirements can usually be installed using your package manager. Make sure it provides the required versions, or download them from the official sites.
* Git
* VirtualBox 4.0 or later ([generic binaries](https://www.virtualbox.org/wiki/Downloads))
* Ansible 1.4 or later (also available on pip: `pip install ansible`)
* Vagrant 1.7 or later

## Usage Instructions

### Imouto for development:

- Install the requirements above
- Clone the github repo on your machine:

```bash
$ git clone https://github.com/Tatoeba/imouto.git
```

- Use vagrant to first download the box and then provision it using ansible. Please be patient, it takes a while for vagrant to download the ~300MB box on your machine and then to provision it using ansible.

```bash
$ cd imouto #ignore if already in the imouto directory
```

- Configure proxy on Vagrant VM if you are behind proxy server:
  - Install proxyconf plugin:

```bash
$ vagrant plugin install vagrant-proxyconf
```

  - And then add the following to Vagrantfile:

```ruby
Vagrant.configure("2") do |config|
  if Vagrant.has_plugin?("vagrant-proxyconf")
    config.proxy.http     = "http://username:password@proxy_host:proxy_port"
    config.proxy.https    = "http://username:password@proxy_host:proxy_port"
    config.proxy.no_proxy = "localhost,127.0.0.1,.example.com"
  end
end
```

```bash
$ vagrant up
```

- Once it completed, you should be able to:
  - Access your local instance of Tatoeba at http://localhost:8080/
  - Run `vagrant ssh` to ssh to the machine.
  - Use the script `mount.sh` (run it to get usage instructions) to mount any of the VM's directory on your host machine in order to modify files without ssh-ing to the VM. (Use 'vagrant' as the password if prompted after running `mount.sh`: `vagrant@127.0.0.1's password:`)

### Post-provisioning tasks

You may want to perform certain tasks independently without having to re-provision the whole machine again. To do that you can use the following command:

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

The following playbooks are included with imouto currently:

- `local.yml`: To provision the whole machine (development) by calling each of the roles. Note that this can also be achieved by running `vagrant provision` command in the `imouto` directory.
- `setup_lemp.yml`: To install and set up nginx, mysql and php5-fpm.
- `update_code.yml`: To fetch the latest code from Tatoeba's github repository and update it on VM.
- `setup_database.yml`: To re-initialize the whole Tatoeba database.
- `setup_external_tools.yml`: To install and set up the external tools used by the website including sphinx and imagick.
- `configure_sphinx.yml`: To configure sphinx search, create indexes and start the search daemon.
- `setup_newrelic.yml`: To install and setup New Relic monitoring daemons.
- `backup.yml`: To create a backup of the database, configurations and other static files of the VM on your machine.
- `restore.yml`: To restore the backup created using `backup.yml`.
- `restore_version.yml`: To restore to a particular revision of code that is available in `versions/` directory

##### Usage

The `restore.yml` playbook needs the name of the backup file to be restored, which can be specified in the `host_vars/default` file or through command line argument like this:

```bash
$ imouto-devel -e restore_src=path/to/backup/file.tar.gz restore.yml
```

The `restore_version` playbook needs the version number of the version to restore to. The versions are numbered from 1 to N where 1 is the latest version and N is oldest. The maximum value of N depends on the variable `revision_limit` that can be set in `host_vars/default`. If you enter an invalid value for `version`, the playbook will throw an error and tell you the possible valid values for it. The playbook can be run as follows:

```bash
$ imouto-devel -e version=3 restore_version.yml
```

##### Note:

- This project is still in its development stage, so there are high chances of bugs. Please report them through github's bug tracker.
- There are a lot of variables defined in `ansible/host_vars/default` that allow specifying further parameters related to various tools. Though the default values just work, it is recommended to change these values according to your need.

### Imouto for production:

The same set of scripts of imouto (with a few changes) can be used for setting up production servers as well. You need to follow these steps:

- Install the requirements above
- Clone the github repo on your machine (this should be different from the repo cloned for development server):

```bash
$ git clone https://github.com/Tatoeba/imouto.git
```

- Edit `imouto/ansible/ansible.cfg`:
    * Uncomment the line `#ask_sudo_pass = True` if you want to enter sudo password through prompt. Alternatively, you can also specify `-K` flag with the `ansible-playbook` command given below to do so.
    * Uncomment the line `#ask_pass = True` if you want to enter ssh password through prompt. Alternatively, you can also specify `-k` flag with the `ansible-playbook` command given below to do so. If you want to avoid typing ssh password repeatedly, you can set up passwordless ssh using the method described [here](http://www.linuxproblem.org/art_9.html).
- Edit `imouto/ansible/host_vars/tatoeba`:
    * Set value of `ansible_ssh_user` to ssh user (the user with which you want to ssh).
    * Uncomment `ansible_ssh_port: 3022` and set the correct port number if you want to use a port other than `22` for ssh.
- Edit `imouto/ansible/production-server` and replace `127.0.0.1` with the address of the server.
- Run ansible-playbook command to run a playbook:

```bash
$ cd imouto/ansible
$ ansible-playbook -i production-server playbook.yml
```

  To set up the whole server you can use `production.yml` playbook or to perform individual tasks you can use the other playbooks provided with imouto.

