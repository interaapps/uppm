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
`ÌNFO: IF YOU HAVE INSTALLED UPPM GLOBALLY YOU WONT NEED php before uppm. Example: uppm init`
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