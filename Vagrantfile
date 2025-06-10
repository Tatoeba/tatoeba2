# -*- mode: ruby -*-
# vi: set ft=ruby et:

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.network :forwarded_port, guest: 80, host: 8080   # HTTP
  config.vm.synced_folder '.', '/vagrant', disabled: true
  config.vm.synced_folder '.', '/home/vagrant/Tatoeba'
  webserver_writable_dirs = [
    'logs',
    'tmp',
    'exported_files',
    'webroot/img/profiles_128',
    'webroot/img/profiles_36',
    'webroot/files/audio',
    'webroot/files/audio_import',
  ]
  for dir in webserver_writable_dirs
    config.vm.synced_folder "./#{dir}", "/home/vagrant/Tatoeba/#{dir}",
                            group: 'www-data',
                            mount_options: ["dmode=775,fmode=664"]
  end

  config.vm.provider "virtualbox" do |v|
    # Here you can adjust RAM allocated to the VM:
    v.memory = 1024 # in MB
  end

  if ENV['BUILD'] == '1'
    config.vm.box = "debian/bullseye64"
    config.vm.provision "install", :type => "ansible" do |ansible|
      # ansible.verbose = "vvvv"
      ansible.playbook = "ansible/vagrant.yml"
    end
    config.vm.provision "strip", :type => "shell", :path => "reduce_box_size.sh"
  else
    config.vm.box = "tatoeba/tatovm"
    config.vm.box_version = "0.1.0"
    config.vm.provision "install",
                        :type => "shell",
                        :privileged => false,
                        :path => "tools/codeinit.py",
                        :args => ["/home/vagrant/Tatoeba"]

    config.vm.provision "shell", inline: <<-SHELL
      apt-get update && apt-get -y upgrade && apt-get -y dist-upgrade
    SHELL

    config.vm.provision "db_backup",
                        :type => "shell",
                        :run => "never", # because this is executed from triggers
                        :inline => "mysqldump --all-databases | gzip > /home/vagrant/Tatoeba/databases.sql.gz"
    config.vm.provision "db_restore",
                        :type => "shell",
                        :run => "never", # because this is executed from triggers
                        :inline => "zcat /home/vagrant/Tatoeba/databases.sql.gz | mysql"

    config.trigger.before :destroy do |trigger|
      trigger.ruby do |env,machine|
        env.ui.warn("Warning: you are about to destroy the virtual machine.\n" +
                    "Your code is safe, but the database will be deleted.")
        answer = env.ui.ask("Would you like to backup the database before destroying the machine? [n/Y] ")
        answer = answer.strip.downcase
        answer = "y" if answer.to_s.empty?
        if answer != "n"
          options = {}
          options[:provision_types] = [ :db_backup ]
          action = machine.action(:provision, options)
          if action[:result] == false
            env.ui.error("Backup failed, aborting. Make sure the virtual machine is running.")
            abort
          else
            env.ui.info("Databases backuped into databases.sql.gz. Will be restored upon new machine creation.")
          end
        end
      end
    end

    config.trigger.after :up do |trigger|
      trigger.ruby do |env,machine|
        path = File.join(env.cwd, "databases.sql.gz")
        if File.exist?(path)
          env.ui.warn("A database backup file was found: #{path}")
          answer = env.ui.ask("Would you like to restore that backup into the virtual machine? [n/Y]: ")
          answer = answer.strip.downcase
          answer = "y" if answer.to_s.empty?
          if answer != "n"
            options = {}
            options[:provision_types] = [ :db_restore ]
            action = machine.action(:provision, options)
            if action[:result] == false
              env.ui.error("Restore failed!")
            else
              env.ui.info("Restore complete.")
              env.ui.warn("Removing #{path}...")
              File.unlink(path)
            end
          end
        end
      end
    end
  end
end
