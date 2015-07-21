# -*- mode: ruby -*-
# vi: set ft=ruby :
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.provision :shell, path: "provisioning/vagrant/provision.sh"

  # Force nat dns host resolver
  config.vm.provider :virtualbox do |vb|
    vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    vb.memory = 512
    vb.cpus = 1
  end

  # Create a private network with a random Class-C IP address
  config.vm.network "private_network", ip: "192.168.250.#{2 + rand(255)}"
  config.vm.hostname = "fansubebooks.chaostangent.local"
  # Use hostmanager plugin to update hosts file
  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true

  # This won't work on Windows machines, maybe try SMB with appropriate changes
  # to the provisioning shell script. If you use VirtualBox shared folders
  # be prepared to wait a whole lot for each request
  config.vm.synced_folder ".", "/vagrant", type: "nfs"
end
