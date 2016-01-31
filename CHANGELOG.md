Changelog
=========

**1.7.0**

* Added support for writing to output. This is backwards compatible for any migration that uses the
  trait `Mongrate\Migration\Migration`, as suggested by our example migration. Usage:

        public function up(Database $db)
        {
            // ... your migration code here.
            // ... say you are adding a field to a user, the usage would be:
            $this->output->writeln(sprintf('Added field %s to user %s with value %s', 'newFieldName', $user['email'], $newValue));
        }

**1.6.2**

* Added support for DBRef objects in YML test files. Example usage:

        Cities:
            -
                name: Marlow
                country: MongoDBRef(Countries,aaaaaaaaaaaa111111111111)
        Countries:
            -
                _id: MongoId(aaaaaaaaaaaa111111111111)
                name: United Kingdom
                continent: MongoDBRef(Continents,bbbbbbbbbbbb222222222222)
        Continents:
            -
                _id: MongoId(bbbbbbbbbbbb222222222222)
                name: Europe

**1.6.0**

* Added new command, `self-update`, which updates the executable to the latest version.
* Misc bug fixes and user experience improvements.
* Ended official testing support for PHP 5.4.
* The executable Phar file is now about 50% smaller.

**1.5.0**

* Added new command, `test-all`, which tests all migrations.

**1.4.0**

* The project has a new official website: http://mongrate.com/
* Added new command, `up-all`, which applies all remaining migrations.
* New recommended method of installation that doesn't require the downloading of the source code.
  See [mongrate.com/docs/installation](http://mongrate.com/docs/installation)
* New recommended method of configuration: `/etc/mongrate.yml`.
  See [mongrate.com/docs/installation](http://mongrate.com/docs/installation)
* Internal refactoring allows extension of a service, and re-use of code internally.

**1.3.10**

* Added `--force` option to the `up` and `down` commands.
* Added `--pretty` option to the `test` command.
* Added validation for migration names, to prevent names over 49 characters being entered (they cause problems).

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
