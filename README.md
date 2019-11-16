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

#### Repositories
```json
{
    "name": "uppm",
    ...
    "repositories": {
        "myrepos": "https://my.repos/repository.json"
    }
}
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
```json
{
    "name": "uppm",
    ...
    "serve": {
        "directory": "public", // default . (Project directory)
        "routerFile": "index.php", // default: none.
        "host": "0.0.0.0", // default: 0.0.0.0 (localhost,127.0.0.1)
        "port": 8000 // Default: 8000
    }
}
```