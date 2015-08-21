How to contribute
=================

Please submit pull requests [on GitHub](https://github.com/mongrate/mongrate/pulls).

The project follows the PSR2 standard.

## Set up your development environment

```bash
php composer.phar install
cp config/parameters.yml.dist config/parameters.yml
# Verify your environment is ready:
bin/phpunit
```

## Pre-commit hook

When you do `composer install` it should set a Git pre-commit hook to help you follow the standards.

To install the Git pre-commit hook manually:

```sh
bash contrib/setup.sh
```

## How to run the tests

To run the test suite, just run `phpunit` or `bin/phpunit`.

The tests use a database called `mongrate_test` in your local MongoDB server.
