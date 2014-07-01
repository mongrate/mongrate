#!/usr/bin/env php
<?php
/*
 * Mongrate bootloader
 */

chdir(__DIR__);

require_once 'vendor/autoload.php';

$loader = new \Symfony\Component\ClassLoader\UniversalClassLoader();
$loader->registerNamespaces(array(
    'Mongrate' =>  'src'
));
$loader->register();

$app = new \Symfony\Component\Console\Application();
$app->add(new \Mongrate\Command\MigrateCommand);
$app->add(new \Mongrate\Command\GenerateMigrationCommand);
$app->run();
