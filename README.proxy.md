# How to use vagrant with a proxy

- Install proxyconf plugin:

```bash
 vagrant plugin install vagrant-proxyconf
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
