# PHPMailer for Ulole-Framework

This is simple implimitation of [PHPMailer](https://github.com/PHPMailer/PHPMailer) made by Marcus Bointon, Jim Jagielski, Andy Prevost and Brent R. Matzelle (The names are by the README.md from 28 August 2019) for the ulole-framework

## Getting started
conf.json
```json
{
    "modules": {
        "github:phpmailer": "https://github.com/interaapps/PHPMailer-Ulole-Module"
    }
}

```

Code Example
```php
<?php
$mail = new modules\phpmailer\PHPMailer;
$mail->isSMTP();
$mail->Host = 'SERVER';
$mail->Port = 25;
$mail->SMTPAuth = true;
$mail->Username = 'USERNAME';
$mail->Password = 'PASSWORD';
$mail->setFrom('test@test.com', 'Hello');

$mail->addAddress("test2@test.com");

$mail->Subject = 'World';

$mail->msgHTML("Hello World!!!");
if ($mail->send())
    echo "sent"; 
else 
    die("".$mail->ErrorInfo); 

```

Then execute this in the console of the directory of your ulole project:
```bash
php cli modules install
```
