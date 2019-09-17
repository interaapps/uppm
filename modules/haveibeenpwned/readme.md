# HaveIBeenPwned API for ulole

This is a HaveIBeenPwned module for the PHP framework ulole

```php
<?php
$count = \modules\haveibeenpwned\HaveIBeenPwned::passwords("password");
```

conf.json
```json
{
    "modules": {
        "github:haveibeenpwned": "https://github.com/interaapps/haveibeenpwned-ulole-module"
    }
}

```

Then execute this in the console of the directory of your ulole project:
```bash
php cli modules install
```
