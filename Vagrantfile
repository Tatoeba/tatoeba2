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
    config.vm.box = "debian/buster64"
    config.vm.provision "install", :type => "ansible" do |ansible|
      # ansible.verbose = "vvvv"
      ansible.playbook = "ansible/vagrant.yml"
    end
    config.vm.provision "strip", :type => "shell", :path => "reduce_box_size.sh"
  else
    config.vm.box = "tatoeba/tatoeba"

    # Set these as :run => "never" because they are manually executed from triggers
    config.vm.provision "db_backup", :type => "ansible", :run => "never" do |ansible|
      ansible.playbook = "ansible/db_backup.yml"
    end
    config.vm.provision "db_restore", :type => "ansible", :run => "never" do |ansible|
      ansible.playbook = "ansible/db_restore.yml"
    end

    config.trigger.before :destroy do |trigger|
      trigger.ruby do |env,machine|
        env.ui.warn("Warning: you are about to destroy the virtual machine.\n" +
                    "Make sure to commit and push any ongoing work first.\n" +
                    "Also note that the database will be deleted.")
        answer = env.ui.ask("Would you like to backup the database before destroying the machine? [n/Y] ")
        answer = answer.strip.downcase
        answer = "y" if answer.to_s.empty?
        if answer != "n"
          options = {}
          options[:provision_types] = [ :db_backup ]
          machine.action(:provision, options)
        end
      end
    end

    config.trigger.after :up do |trigger|
      trigger.ruby do |env,machine|
        path = File.join(env.cwd, "ansible", "databases.sql.gz")
        if File.exist?(path)
          env.ui.warn("A database backup file was found: #{path}")
          answer = env.ui.ask("Would you like to restore that backup into the virtual machine? [n/Y]: ")
          answer = answer.strip.downcase
          answer = "y" if answer.to_s.empty?
          if answer != "n"
            options = {}
            options[:provision_types] = [ :db_restore ]
            machine.action(:provision, options)
          end
          env.ui.warn("Removing #{path}...")
          File.unlink(path)
        end
      end
    end
  end
end
