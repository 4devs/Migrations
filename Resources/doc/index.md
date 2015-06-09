Getting Started With Migrations Library
=======================================

## Installation and usage

Installation and usage is a quick:

1. Download Migrations library using composer
2. Use the Migrations library
3. Usage with symfony [console](https://github.com/symfony/Console)
4. Usage with mongodb collection config


### Step 1: Download Download Migrations library using composer

Add Migrations Library in your composer.json:

```js
{
    "require": {
        "fdevs/migrations": "*"
    }
}
```

Now tell composer to download the library by running the command:

``` bash
$ php composer.phar update fdevs/migrations
```

Composer will install the bundle to your project's `vendor/fdevs` directory.


### Step 2: Base usage the library

#### create migration

```php
<?php

namespace App\Migrations;

use FDevs\Migrations\Migration\MongodbMigration;

class Version20150601103845 extends MongodbMigration
{
    public function up()
    {

    }

    public function down()
    {

    }
}
```

#### run migration

```php
<?php
require __DIR__.'/../vendor/autoload.php';

use FDevs\Migrations\Configuration\FilesConfiguration;
use FDevs\Migrations\Migration\MongodbMigration;
use FDevs\Migrations\Migration;
use FDevs\Migrations\Provider\MongodbProvider;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\MongoDB\Connection;

$dm = DocumentManager::create(new Connection(), new Configuration());
$provider = new MongodbProvider($dm);
$migrationsDirs = [realpath(__DIR__.'/../src/App/Migrations')];
$cacheDir = realpath(__DIR__.'/../var/cache');
$config = new FilesConfiguration($migrationsDirs, $cacheDir, $provider);

$migration = new Migration($config);
//run all migration from current  
$migration->migrate();
```

### Step 3: Usage with symfony console

add command

```php
#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/vendor/autoload.php';

use Acme\Console\Command\GreetCommand;
use Symfony\Component\Console\Application;
use FDevs\Migrations\Console\Command\InfoCommand;
use FDevs\Migrations\Console\Command\MigrateCommand;
use FDevs\Migrations\Configuration\FilesConfiguration;
use FDevs\Migrations\Migration\MongodbMigration;
use FDevs\Migrations\Migration;
use FDevs\Migrations\Provider\MongodbProvider;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\MongoDB\Connection;


$provider = new MongodbProvider($dm);
$migrationsDirs = [realpath(__DIR__.'/../src/App/Migrations')];
$cacheDir = realpath(__DIR__.'/../var/cache');
$config = new FilesConfiguration($migrationsDirs, $cacheDir, $provider);

$info = new InfoCommand();
$info->setMigrationConfiguration($config);
$migrate = new MigrateCommand();
$migrate->setMigrationConfiguration($config);

$application = new Application();
$application->add($migrate);
$application->add($info);
$application->run();
```

run

```bash
$ php application.php fdevs:migrations:info
$ php application.php fdevs:migrations:migrate
```

### Step 4: Usage with mongodb collection config

```php
<?php
require __DIR__.'/../vendor/autoload.php';

use FDevs\Migrations\Configuration\MongodbConfiguration;
use FDevs\Migrations\Migration\MongodbMigration;
use FDevs\Migrations\Migration;
use FDevs\Migrations\Provider\MongodbProvider;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\MongoDB\Connection;

$dm = DocumentManager::create(new Connection(), new Configuration());
$provider = new MongodbProvider($dm);
$migrationsDirs = [realpath(__DIR__.'/../src/App/Migrations')];
$collection = '_fdevs_migrations';
$config = new MongodbConfiguration($migrationsDirs, $provider, $dm, $collection);

$migration = new Migration($config);
//run all migration from current  
$migration->migrate();
```
