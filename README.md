Mongrate
========

A command-line tool for applying migrations to a MongoDB database. Migrations don't have to be linear, so you don't need to apply them in any order.

Doctrine's [Mongo abstraction layer](https://github.com/doctrine/mongodb) is used to provide a clean database API. Mongrate does *not* use Doctrine's Mongo ODM - this is to avoid having to write mapping classes and to make it very quick to write migrations.

At the moment, Mongrate does not support applying migrations to multiple databases.

Example migrations can be found in `resources/examples/`.

Installation
============

    php composer.phar install
    chmod a+x mongrate.php
    cp config/parameters.yml.dist config/parameters.yml

Edit `config/parameters.yml` and enter your MongoDB connection information.

Usage
=====

To generate a migration file, with the name "UpdateAddressStructure":

    ./mongrate.php generate-migration UpdateAddressStructure

To toggle a migration (useful while writing your migration):

    ./mongrate.php toggle 20140523_UpdateAddressStructure

To migrate up or down:

    ./mongrate.php up 20140523_UpdateAddressStructure

    ./mongrate.php down 20140523_UpdateAddressStructure

Contributing
============

Please submit pull requests [on GitHub](https://github.com/amyboyd/mongrate/pulls).

Install [PHP-CS-Fixer](https://github.com/fabpot/PHP-CS-Fixer):

    sudo curl http://get.sensiolabs.org/php-cs-fixer.phar -o /usr/local/bin/php-cs-fixer

Install the Git pre-commit hook:

    ln -s ../../resources/pre-commit .git/hooks/pre-commit
    chmod a+x .git/hooks/pre-commit
