# -*- mode: ruby -*-
# vi: set ft=ruby et:

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "debian/stretch64"
  config.vm.network :forwarded_port, guest: 80, host: 8080
  config.vm.synced_folder '.', '/vagrant', disabled: true
end

Vagrant.configure("2") do |config|
  config.vm.provision "ansible" do |ansible|
    # ansible.verbose = "vvvv"
    ansible.playbook = "ansible/local.yml"
  end
end
