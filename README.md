Mongrate
========

[![Build Status](https://travis-ci.org/amyboyd/mongrate.svg?branch=master)](https://travis-ci.org/amyboyd/mongrate)

A command-line tool for applying migrations to a MongoDB database. Migrations are non-linear, and can be tested by writing simple YML files.

Doctrine's [Mongo abstraction layer](https://github.com/doctrine/mongodb) is used to provide a clean database API. Mongrate does *not* use Doctrine's Mongo ODM - this is to avoid having to write mapping classes and to make it very quick to write migrations.

At the moment, Mongrate does not support applying migrations to multiple databases.

Example migrations can be found in `resources/examples/`.

Symfony 2 users can use [MongrateBundle](https://github.com/amyboyd/mongrate-bundle) to integrate easily with a Symfony 2 project.

Installation
============

Mongrate is available [via Composer](https://packagist.org/packages/amyboyd/mongrate).

Once you have downloaded Mongrate, run these commands:

    php composer.phar install
    chmod a+x mongrate
    cp config/parameters.yml.dist config/parameters.yml

Edit `config/parameters.yml` and enter your MongoDB connection information.

Usage
=====

To generate a migration file, with the name "UpdateAddressStructure":

    ./mongrate generate-migration UpdateAddressStructure

To list available migrations:

    ./mongrate list-migrations

To toggle a migration (useful while writing your migration):

    ./mongrate toggle 20140523_UpdateAddressStructure

To migrate up or down:

    ./mongrate up 20140523_UpdateAddressStructure

    ./mongrate down 20140523_UpdateAddressStructure

To verify a migration with it's YML test files:

    ./mongrate test 20140523_UpdateAddressStructure (up|down|empty)

Contributing
============

Please submit pull requests [on GitHub](https://github.com/amyboyd/mongrate/pulls).

Install [PHP-CS-Fixer](https://github.com/fabpot/PHP-CS-Fixer):

    sudo curl http://get.sensiolabs.org/php-cs-fixer.phar -o /usr/local/bin/php-cs-fixer

Install the Git pre-commit hook:

    ln -s ../../resources/pre-commit .git/hooks/pre-commit
    chmod a+x .git/hooks/pre-commit

To run the test suite, just run `phpunit`. The tests use a database called `mongrate_test` in your local MongoDB server.
