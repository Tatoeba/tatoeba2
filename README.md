# Imouto

Imouto is a collection of automation scripts for building an instance of [Tatoeba](https://tatoeba.org/) using [ansible](http://www.ansible.com/home). This is the preferred way for developers to setup a local development environment.

## Requirements

Here are the basic requirements of the machine you’re using imouto from.

* GNU/Linux or MacOS
* Git
* VirtualBox 4.0 or later, which can be installed with a package manager or with the help of [generic binaries](https://www.virtualbox.org/wiki/Downloads))
* Vagrant 1.7 or later

## Usage Instructions

- Install the requirements above.

- Clone the repository and go to its directory:

```bash
git clone https://github.com/Tatoeba/imouto
cd imouto
```

- If you need to use a proxy, follow the instructions in `README.proxy.md`.

- Run this command to download and start up the Tatoeba VM. Please be patient, it takes a while.

```bash
vagrant up
```

- Once it completed, you should be able to:
  - Access your local instance of Tatoeba at http://localhost:8080/
  - Run `vagrant ssh` to ssh to the machine.
  - Use the script `mount.sh` (run it to get usage instructions) to mount any of the VM's directory on your host machine in order to modify files without ssh-ing to the VM. (Use 'vagrant' as the password if prompted after running `mount.sh`: `vagrant@127.0.0.1's password:`)
