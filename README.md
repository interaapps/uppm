<p align="center"><img src="./src/resources/ulole.svg" width="200"><br>Ulole-PHP-Package-Manager</p>


```shell
# Creating an boilerplate app
$ uppm create testapp
// Using template (For example interaapps/ulole-framework)
$ uppm create testapp interaapps/ulole-framework:3.2.0
INFO: Creating in test123
$ cd testapp

# Start your app
$ uppm run start
Hello World!

# Installing modules
$ uppm install uloleorm
# Or from github
$ uppm install interaapps/ulole-orm+master@github
# Or from composer
$ uppm install interaapps/uloleorm@composer
# Or more...

# Building your app (Useful for CLI-Apps or non-web-hosted stuff)
$ uppm build
INFO: Creating phar...
[...]
$ cd target
$ ./testapp-1.0.phar
Hello World

# Serve (Useful if you are developing a Web-App)
uppm serve
```

## For a single project
```shell
wget -O uppm.phar https://raw.githubusercontent.com/interaapps/uppm/master/target/uppm.phar
php uppm.phar help
```
## Globally
```shell
wget -O uppm https://raw.githubusercontent.com/interaapps/uppm/master/target/uppm.phar
# Installing it on linux globally
sudo mv uppm /usr/local/bin/uppm
sudo chmod +x /usr/local/bin/uppm
uppm help
```

### Requirements
- php8.0
- php8.0-zip
- php8.0-json
- php8.0-phar (And enabled in php.ini, `/etc/php/8.0/cli/php.ini`, `phar.readonly = Off`)
```shell
# Installing dependencies
sudo apt install php8.0 php8.0-zip php8.0-json php8.0-phar

# Adding phar rule to php.ini (For building projects)
sudo echo phar.readonly = Off >> /etc/php/8.0/cli/php.ini
```

### uppm.json

```json
{
  "name": "uppm",
  "version": "1.0",
  "phpVersion": ">8.0",
  "repositories": [],
  "run": {
    "start": "src\/main\/bootstrap.php",
    "install": "src/scripts/install.php"
  },
  "build": {
    "type": "phar",
    "run": "start",
    "outputName": "uppm",
    "ignored": [
      "test.txt",
      ".git"
    ]
  },
  "modules": {
  },
  "namespaceBindings": {
    "de\\interaapps\\uppm": "src\/main\/de\/interaapps\/uppm"
  }
}
```

### autoload

```shell
# adds a autoload.php file
uppm autoload
```

```php
<?php
(include 'autoload.php')();
```

# Docker
```shell
docker pull interaapps/uppm
```