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

Contributing
============

Please submit pull requests on GitHub.

Install PHP-CS-Fixer:

    sudo curl http://get.sensiolabs.org/php-cs-fixer.phar -o /usr/local/bin/php-cs-fixer

Install the Git pre-commit hook:

    ln -s ../../resources/pre-commit .git/hooks/pre-commit
    chmod a+x .git/hooks/pre-commit
