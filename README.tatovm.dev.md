# This is for developpers of TatoVM

This guide is for developpers of TatoVM. If you want to install a local instance of Tatoeba, please refer to the file `README.tatovm.md` instead.

## Requirements

Here are the basic requirements of the machine you’re using TatoVM from.

* GNU/Linux or MacOS
* Git
* Ansible 2.4 or later (also available on pip: `pip install ansible`)
* VirtualBox 4.0 or later, which can be installed with a package manager or with the help of [generic binaries](https://www.virtualbox.org/wiki/Downloads))
* Vagrant 1.7 or later

## Building a local VM

- Install the requirements above.

- Clone the repository and go to its directory:

```bash
git clone https://github.com/Tatoeba/tatoeba2
cd tatoeba2
```

- If you need to use a proxy, follow the instructions in `README.proxy.md`.

- Run these commands to install everything. Please be patient, it takes a while for vagrant to download the ~300MB box on your machine and then to provision it using ansible.

```bash
BUILD=1 vagrant box update
BUILD=1 vagrant up
```

## Post-provisioning tasks

You may want to perform certain tasks or steps independently without having to re-provision the whole machine again. To do that you can use the following command:

```bash
ansible-playbook -i .vagrant/provisioners/ansible/inventory/vagrant_ansible_inventory --private-key=~/.vagrant.d/insecure_private_key ansible/vagrant.yml --tag <tag>
```

where `<tag>` is one of the tags present in the file `ansible/tatoeba.tasks.yml`. You can also use `--skip-tag` to run all the tasks *but* one in particular. Since the command is too long and very difficult to remember, you can use the following commands to create an alias:

```bash
echo "alias tatovm-provision='ansible-playbook -i .vagrant/provisioners/ansible/inventory/vagrant_ansible_inventory --private-key=~/.vagrant.d/insecure_private_key ansible/vagrant.yml'" >> ~/.bashrc
source ~/.bashrc
```

Now you can simply use the following command to run a particular step:

```bash
tatovm-provision --tag external_tools
```

## Package the VM

Export the VM into a file

```bash
vagrant package --output tatoeba.box
```

## Test new VM version without publishing it yet

The commands below assume the new box version is 0.2.0.

Create a json file describing the box:

```bash
newversion=0.2.0
echo '{"name":"tatoeba/tatovm","versions":[{"providers":[{"name":"virtualbox","description":"","url":"./tatoeba.box"}],"version":"'$newversion'"}]}' > /tmp/box.json
```

Then run this command to let Vagrant know about the new version:

```bash
vagrant box add /tmp/box.json
```

Now the new version should show up in the output of `vagrant box list`.

Then:

```bash
# Move to a new directory
mkdir ../test_tatovm/
cd ../test_tatovm/

# Pull a fresh copy of Tatoeba
# Tip: add -b <branch> to pull a specific branch
git clone --depth 1 https://github.com/Tatoeba/tatoeba2

cd tatoeba2

# Edit ./Vagrantfile to bump config.vm.box_version to 0.2.0 in ./Vagrantfile

# You will probably have to empty caches to work around VERR_NO_LOW_MEMORY error
echo 3 | sudo tee /proc/sys/vm/drop_caches

# Finally start the VM
vagrant up
```

## Publish the VM

- Log into https://portal.cloud.hashicorp.com/

- Click "Vagrant Registry"

- Open "tatoeba/tatovm"

- Click "Versions"

- Add a new version

  - Provider should be `virtualbox`
  - Architecture should be `amd64`
  - Use SHA256 as hash and run `sha256sum tatoeba.box` to get it
  - Upload the exported tatoeba.box file

- Release the new version

- Push ./Vagrantfile with `config.vm.box_version` updated to the new version
