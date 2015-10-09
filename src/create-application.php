<?php

/*
 * Create a console application with the Mongrate commands.
 */

if (!extension_loaded('mongo')) {
    // Instead of throwing a \RuntimeException, which isn't friendly when running the .phar file
    // for the first time when trying to get set up, exit with an error message instead.
    echo "The MongoDB extension must be installed.\n";
    echo "See https://secure.php.net/manual/en/mongo.installation.php\n";
    exit(1);
}

require_once 'vendor/autoload.php';

$loader = new \Symfony\Component\ClassLoader\UniversalClassLoader();
$loader->registerNamespaces(array(
    'Mongrate' => 'src',
));
$loader->register();

$app = new \Symfony\Component\Console\Application();
$app->add(new \Mongrate\Command\ToggleMigrationCommand());
$app->add(new \Mongrate\Command\UpCommand());
$app->add(new \Mongrate\Command\DownCommand());
$app->add(new \Mongrate\Command\GenerateMigrationCommand());
$app->add(new \Mongrate\Command\ListCommand());
$app->add(new \Mongrate\Command\TestMigrationCommand());
$app->add(new \Mongrate\Command\UpAllCommand());

$app->setName('Mongrate migration tool');

if (defined('MONGRATE_VERSION')) {
    // Running in the Phar.
    $app->setVersion(MONGRATE_VERSION);
} else {
    // Running in dev mode.
    $app->setVersion(require_once __DIR__ . '/get-version.php');
}

// `$app->run()` cannot be called in this file, because this file is included by the tests.
