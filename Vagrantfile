# -*- mode: ruby -*-
# vi: set ft=ruby et:

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.network :forwarded_port, guest: 80, host: 8080   # HTTP
  config.vm.network :forwarded_port, guest: 2049, host: 8049 # NFS
  config.vm.synced_folder '.', '/vagrant', disabled: true

  config.vm.provider "virtualbox" do |v|
    # Adjust RAM allocated to the VM
    # The value should be at least 1/4 of your machine's actual RAM.
    v.memory = 2048 # in MB

    if Vagrant::Util::Platform.windows?
      # configure private network for samba share
      # should be accessible at \\172.19.119.178\tatoeba
      config.vm.network "private_network", :adapter => 2, :type => "static",
                        :ip => "172.19.119.178", :netmask => "255.255.255.252",
                        :adapter_ip => "172.19.119.177"
    end
  end

  if !Vagrant::Util::Platform.windows?
    config.trigger.before :halt, :suspend do |trigger|
      trigger.info = "Unmounting NFS directory `Tatoeba' if mounted..."
      trigger.run = {inline: "sh -c 'mountpoint -q Tatoeba || exit 0 && umount Tatoeba'"}
    end
    config.trigger.after :up do |trigger|
      trigger.info = "Mounting NFS directory `Tatoeba' if configured..."
      trigger.run = {inline: "sh -c '[ -f Tatoeba/empty -a -f /etc/fstab ] && grep -q \"^localhost:/home/vagrant/Tatoeba[[:space:]]\\+$PWD/Tatoeba\" /etc/fstab && mount Tatoeba || true'"}
    end
  end

  if ENV['BUILD'] == '1'
    config.vm.box = "debian/stretch64"
    config.vm.provision "install", :type => "ansible" do |ansible|
      # ansible.verbose = "vvvv"
      ansible.playbook = "ansible/vagrant.yml"
    end
    config.vm.provision "strip", :type => "shell", :path => "reduce_box_size.sh"
  else
    config.vm.box = "tatoeba/tatoeba"
  end
end
