# Install Tatoeba on a local machine

## Intruduction

This setup is for using Tatoeba on a dedicated machine. Please be aware that this will install many things all over the place and may mess up with existing applications like nginx, mysql etc.

The steps are roughly the same as in `README.dev.md`, except the additional requirements are:

* Debian Stretch
* sudo

Note that Debian Stretch only packages Ansible 2.2 whereas we need 2.4. You can follow [these instructions](https://docs.ansible.com/ansible/latest/installation_guide/intro_installation.html#latest-releases-via-apt-debian) to get Ansible 2.4 on your Debian Stretch machine.

## Usage Instructions

- Move to the ansible directory

```sh
cd ansible
```

- Edit the file `host_vars/default` to set some variables according to your needs, such as:

```
code_dir: /home/johndoe/tatoeba/
git_rep: https://github.com/myfork/tatoeba2
```

- Install everything

```sh
ansible-playbook ./local.yml
```
