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
