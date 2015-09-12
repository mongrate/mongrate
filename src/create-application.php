<?php

/*
 * Create a console application with the Mongrate commands.
 */

require_once 'vendor/autoload.php';

$loader = new \Symfony\Component\ClassLoader\UniversalClassLoader();
$loader->registerNamespaces(array(
    'Mongrate' =>  'src'
));
$loader->register();

$app = new \Symfony\Component\Console\Application();
$app->add(new \Mongrate\Command\ToggleMigrationCommand);
$app->add(new \Mongrate\Command\UpCommand);
$app->add(new \Mongrate\Command\DownCommand);
$app->add(new \Mongrate\Command\GenerateMigrationCommand);
$app->add(new \Mongrate\Command\ListCommand);
$app->add(new \Mongrate\Command\TestMigrationCommand);
$app->setName('Mongrate migration tool');
$app->setVersion(sprintf(
    '%s (%s)',
    substr(`git rev-parse HEAD`, 0, 7),
    date('Y-m-d H:i:s')
));

// `$app->run()` cannot be called in this file, because this file is included by the tests.
