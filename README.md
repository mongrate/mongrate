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
