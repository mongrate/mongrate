Mongrate
========

[![Build Status](https://travis-ci.org/mongrate/mongrate.svg?branch=master)](https://travis-ci.org/mongrate/mongrate)
[![Code Coverage](https://scrutinizer-ci.com/g/amyboyd/mongrate/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/amyboyd/mongrate/?branch=master)

A command-line tool for applying migrations to a MongoDB database. Migrations are non-linear, and can be tested by writing simple YML files.

Doctrine's [Mongo abstraction layer](https://github.com/doctrine/mongodb) is used to provide a clean database API. Mongrate does *not* use Doctrine's Mongo ODM - this is to avoid having to write mapping classes and to make it very quick to write migrations.

At the moment, Mongrate does not support applying migrations to multiple databases.

Example migrations can be found in `resources/examples/`.

Symfony 2 users can use [MongrateBundle](https://github.com/mongrate/mongrate-bundle) to integrate easily with a Symfony 2 project.

Table of content:

* [Installation](#installation)
* [Configuration](#configuration)
* [Usage](#usage)
	* [Generate Migration](#generate-migration)
	* [List Migrations](#list-migrations)
	* [Toggle Migration](#toggle-migration)
	* [Migrate Up](#migrate-up)
	* [Migrate Down](#migrate-down)
	* [Test Migration](#test-migration)
* [Contributing](#contributing)
* [How to run tests](#how-to-run-tests)



Installation
============

Mongrate is available [via Composer](https://packagist.org/packages/amyboyd/mongrate).

Once you have downloaded Mongrate, run these commands:

```sh
php composer.phar install
chmod a+x mongrate
cp config/parameters.yml.dist config/parameters.yml
```

Edit `config/parameters.yml` and enter your MongoDB connection information.

## Configuration

Your `parameters.yml` you should have something similar to this.

```yml
parameters:
    mongodb_server: 'mongodb://localhost:27017'
    mongodb_db: my_db
    migrations_directory: migrations
```

| Parameter     | Required  | Description  |
| ------------- |:-------------:| :-----|
|  mongodb_server  | required | URI for your mongodb server. |
|  mongodb_db  | required | Database name. |
|  migrations_directory  | required | The directory where your migration files are to be kept. |

*Note:* Don't add a trailing slash in `migrations_directory` parameter value.

Usage
=====

## Generate Migration

To generate a migration file, with the name "UpdateAddressStructure":

```sh
./mongrate generate-migration UpdateAddressStructure
```

## List Migrations

To list available migrations:

```sh
./mongrate list-migrations
```

## Toggle Migration

To toggle a migration (useful while writing your migration):

```sh
./mongrate toggle 20140523_UpdateAddressStructure
```

## Migrate up

To migrate up:

```sh
./mongrate up 20140523_UpdateAddressStructure [--force]
```

## Migrate down

To migrate down:

```sh
./mongrate down 20140523_UpdateAddressStructure [--force]
```

## Test Migration

To verify a migration with it's YML test files:

```sh
./mongrate test 20140523_UpdateAddressStructure (up|down|empty) [--pretty]
```
NOTE: If you leave migration type `empty` after the migration name it will test both migrations `up` and `down`.


Contributing
============

See the file [CONTRIBUTING.md](CONTRIBUTING.md)
