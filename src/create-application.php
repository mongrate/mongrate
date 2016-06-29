<?php

/*
 * Create a console application with the Mongrate commands.
 */

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
$app->add(new \Mongrate\Command\TestAllCommand());
$app->add(new \Mongrate\Command\UpAllCommand());
$app->add(new \Mongrate\Command\SelfUpdateCommand());
$app->setName('Mongrate migration tool');

if (defined('MONGRATE_VERSION')) {
    // Running in the Phar.
    $app->setVersion(MONGRATE_VERSION);
} else {
    // Running in dev mode.
    $app->setVersion(require_once __DIR__ . '/get-version.php');
}

// `$app->run()` cannot be called in this file, because this file is included by the tests.
