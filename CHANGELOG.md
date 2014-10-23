Changelog
=========

**1.3**

* The `list-migrations` command now shows whether each migration has been applied.

**1.2**

* Feature: Write tests for your migrations in YML format. See `resources/examples/UpdateAddressStructure` for an example. Run the test with `./mongrate test UpdateAddressStructure`

* The structure of the migrations directory has changed to allow future features. To update your migrations directory, run this (change `mv` to `git mv` if your migrations are in a Git reposistory):

        for i in $(ls migrations/*.php); do \
            dir=`sed 's/\.php//' <<< $i`; \
            mkdir $dir; \
            mv $i $dir/Migration.php; \
        done
