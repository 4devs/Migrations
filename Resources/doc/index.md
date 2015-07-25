Getting Started With The Migrations Library
===========================================

The main purpose of this library is to provide a way to easily do data migration during lifetime of the application.
It can also be used to do database scheme migrations, too.

## Installation and usage

Installation and usage are easy:

1. Download the Migrations library using composer
2. Use the Migrations library
3. Usage with the Symfony [Console](https://github.com/symfony/Console) component
4. Usage with the mongodb collection config

### Step 1: Download the Migrations library using composer

Add the Migrations library to your composer.json file:

```js
{
    "require": {
        "fdevs/migrations": "*"
    }
}
```

Now tell composer to download the library by running the next command:

``` bash
$ php composer.phar update fdevs/migrations
```

Composer will download the library into your project's `vendor/fdevs` directory.


### Step 2: Base usage of the library

#### Create a migration class

Each migration class must contain a 14-digit version (YYYYmmddHHiiss).

```php
<?php
// src/App/Migrations/Version20150601103845.php
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

#### Do migration

You need to install `doctrine/mongodb-odm` to work with DocumentManager, see the `suggest` section in the composer.json file.

```php
<?php

require __DIR__.'/../vendor/autoload.php';

use FDevs\Migrations\Configuration\FilesConfiguration;
use FDevs\Migrations\Migration;
use FDevs\Migrations\Provider\MongodbProvider;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\MongoDB\Connection;

$mongoConnection = new Connection();
$mongoConfig = new Configuration();

// don't forget to set up $mongoConfig:
$mongoConfig->setProxyDir(__DIR__ . '/../var/cache/doctrine/Proxies');
$mongoConfig->setProxyNamespace('Proxies');
$mongoConfig->setHydratorDir(__DIR__ . '/../var/cache/doctrine/Hydrators');
$mongoConfig->setHydratorNamespace('Hydrators');
$mongoConfig->setDefaultDB('migration_demo');

$dm = DocumentManager::create($mongoConnection, $mongoConfig);
$provider = new MongodbProvider($dm);
$migrationsDirs = [realpath(__DIR__.'/../src/App/Migrations')];
$cacheDir = realpath(__DIR__.'/../var/cache');
$config = new FilesConfiguration($migrationsDirs, $cacheDir, $provider);

$migration = new Migration($config);

//run all migrations starting from the current version
$migration->migrate();
```

### Step 3: Usage with the Symfony Console component

Create an application or register the migration commands in your application

```php
#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use FDevs\Migrations\Console\Command\InfoCommand;
use FDevs\Migrations\Console\Command\MigrateCommand;
use FDevs\Migrations\Configuration\FilesConfiguration;
use FDevs\Migrations\Migration;
use FDevs\Migrations\Provider\MongodbProvider;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\MongoDB\Connection;

$dm = //DocumentManager::create...
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

Run the commands via

```bash
$ php application.php fdevs:migrations:info
$ php application.php fdevs:migrations:migrate
```

### Step 4: Usage with the mongodb collection config

```php
<?php
require __DIR__.'/../vendor/autoload.php';

use FDevs\Migrations\Configuration\MongodbConfiguration;
use FDevs\Migrations\Migration;
use FDevs\Migrations\Provider\MongodbProvider;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\MongoDB\Connection;

$dm = //DocumentManager::create...
$provider = new MongodbProvider($dm);
$migrationsDirs = [realpath(__DIR__.'/../src/App/Migrations')];
$collection = '_fdevs_migrations';
$config = new MongodbConfiguration($migrationsDirs, $provider, $dm, $collection);

$migration = new Migration($config);
$migration->migrate();
```
