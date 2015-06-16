Mongrate
========

[![Build Status](https://travis-ci.org/amyboyd/mongrate.svg?branch=master)](https://travis-ci.org/amyboyd/mongrate)
[![Code Coverage](https://scrutinizer-ci.com/g/amyboyd/mongrate/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/amyboyd/mongrate/?branch=master)

A command-line tool for applying migrations to a MongoDB database. Migrations are non-linear, and can be tested by writing simple YML files.

Doctrine's [Mongo abstraction layer](https://github.com/doctrine/mongodb) is used to provide a clean database API. Mongrate does *not* use Doctrine's Mongo ODM - this is to avoid having to write mapping classes and to make it very quick to write migrations.

At the moment, Mongrate does not support applying migrations to multiple databases.

Example migrations can be found in `resources/examples/`.

Symfony 2 users can use [MongrateBundle](https://github.com/amyboyd/mongrate-bundle) to integrate easily with a Symfony 2 project.

Table of content:

* [Installation](#installation)
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
./mongrate up 20140523_UpdateAddressStructure
```

## Migrate down

To migrate down:

```sh
./mongrate down 20140523_UpdateAddressStructure
```

## Test Migration

To verify a migration with it's YML test files:

```sh
./mongrate test 20140523_UpdateAddressStructure (up|down|empty)
```
NOTE: If you leave migration type `empty` after the migration name it will test both migrations `up` and `down`.


Contributing
============

Please submit pull requests [on GitHub](https://github.com/amyboyd/mongrate/pulls).

Project follow PSR2 standard. 

When you do `composer install` it should set a `git-precomit` to help you follow the standards.

Install the Git pre-commit hook manually:

```sh
bash contrib/setup.sh
```

## How to run tests

To run the test suite, just run `phpunit`. 

The tests use a database called `mongrate_test` in your local MongoDB server.
