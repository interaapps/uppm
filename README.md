<p align="center"><img src="https://cdn.interaapps.de/ulole/icons/ulole1.svg" width="200"><br>Ulole-PHP-Package-Manager</p>


# UlolePHPPackageManager (UPPM) [Testversion 1]

### Downloading
Execute this command in your project directory
```bash
wget https://raw.githubusercontent.com/interaapps/uppm/master/uppm
``` 

#### Install globally on linux
```bash
sudo wget https://raw.githubusercontent.com/interaapps/uppm/master/uppm
sudo mv uppm /usr/local/bin/uppm
sudo chmod 777 /usr/local/bin/uppm
```
Or
`php uppm linuxglobal` (Also useful for updating)

`IMPORTANT!` Requirements:
    - php7.3
    - php7.3-zip (Install: sudo apt install php7.3-zip)
    - php7.3-json (sudo apt install php7.3-json)
    - phar enabled in the php.ini. Example path: `/etc/php/7.3/cli/php.ini` set `;phar.readonly = On` (num: 1060) to `phar.readonly = Off`!

### Init
```bash
php uppm init
```
`INFO` IF YOU HAVE INSTALLED UPPM GLOBALLY YOU WONT NEED php before uppm. Example: uppm init`
### Package managment
#### Official package
```bash
php uppm install mypackage
``` 
#### Github
```bash
php uppm install github:user/mypackage
``` 
#### Github (Branch)
```bash
php uppm install github:user/mypackage+master
``` 
#### Composer (Packagist)
```bash
php uppm install composer:user/package@v1.0
```
#### Web
```bash
php uppm install web:https://user.com/mypackage.zip
``` 

### Building into phar
```bash
php uppm build
``` 

##### Config (uppm.json)
```json
{
    "name": "uppm",
    "version": "1.0",
    ...

    "build": {
        "main": "main.php",
        "output" (optional): "test.phar",
        "src" (optional): "src",
        "ignored_directories" (optional): [
            ".idea"
        ],
        "ignored_files" (optional): [
            "testfile.php"
        ]
    }
}
```

### Custom repositories
It's just a file on a webserver! A json file!
#### Example list.json
```json
{    
    "julianfun123/uloleorm": {
        "newest": "1.0",
        "1.0": "https://example.com/repos/1.0.zip"
    }
}
```

#### Repository in uppm.json
```json
{
    "name": "uppm",
    ...
    "repositories": {
        "example": "https://example.com/list.json"
    },
    "modules": {
        ...
        "julianfun123/uloleorm": "1.0"
    }
}
```

`Tip: ` If you want to have a private repo you can simply check the http request with a `GET` parameter: https://example.com/list.json?private_key=KEY

### Test Server
#### Starting
```bash
php uppm serve
```

#### Configurating
`INFO` Every option is optionial!


| Option          | Default               |
| --------------- |---------------------- |
| directory       | ./ (project directory) |
| routerfile      | none                  |
| host            | 0.0.0.0 (localhost)   |
| port            | 8000                  |

```json
{
    "name": "uppm",
    ...
    "serve": {
        "directory": "public", 
        "routerFile": "index.php",
        "host": "0.0.0.0",
        "port": 8000 
    }
}
```
