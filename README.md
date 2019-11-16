# UlolePHPPackageManager (UPPM) [Testversion 1]

`THIS IS A TEST VERSION AND NOT FOR USING PURPOSES YET!`

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
    }
}
```