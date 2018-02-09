
# Installation
-----------
Install using composer:

```bash
composer require gh-rboliveira/monolog-telegram  
```



# Usage
it is just like other monolog handlers, you need to pass below optional paramaters to telegramhandler object:
- **$level** - The minimum logging level at which this handler will be triggered
- **$bubble** - Whether the messages that are handled can bubble up the stack or not

# Examples
Now Simply use it like this :

```php
require 'vendor/autoload.php';
use Monolog\Logger;
use gh-rboliveira\TelegramHandler\TelegramHandler;

$log = new Logger('TelegramHandler');
//Create handler
$telegramHandler = new TelegramHandler();

//Add $token - your bot token provided by BotFather
$token = 'XXXXXXXXX:qeQWLElwe_m232WE1K3WEe323eeTw';
$telegramHandler->setBotToken($token);

//Set Receipts - an array with telegram ids
$recipients = ['123456789','987654321'];
$telegramHandler->setRecipients($recipients);

//Set Handler
$log->pushHandler($telegramHandler);

$log->info('hello world !');
/**
* There is 8 level of logging
*/
$log->notice('hello world !');
$log->info('hello world !');
$log->debug('hello world !');
$log->warning('hello world !');
$log->critical('hello world !');
$log->alert('hello world !');
$log->emergency('hello world !');
$log->error('hello world !');


/**
* Optionally you can pass second paramater such as a object
**/
$log->info('user just logged in !',['user'=>$user]);

```

# License
This tool in Licensed under MIT, so feel free to fork it and make it better that it is !
