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
    v.memory = 512 # in MB
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
            env.ui.warn("Removing #{path}...")
            File.unlink(path)
          end
        end
      end
    end
  end
end
