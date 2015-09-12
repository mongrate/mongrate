#!/usr/bin/env php
<?php

$outputFile = 'build/mongrate.phar';

chdir(__DIR__ . '/../');

if (file_exists($outputFile)) {
    unlink($outputFile);
}

require 'vendor/autoload.php';

use Symfony\Component\Finder\Finder;

$phar = new Phar($outputFile, 0, 'mongrate.phar');
$phar->setSignatureAlgorithm(\Phar::SHA1);
$phar->startBuffering();

$phar->addFile('vendor/autoload.php');
$phar->addFile('vendor/composer/autoload_namespaces.php');
$phar->addFile('vendor/composer/autoload_classmap.php');
$phar->addFile('vendor/composer/autoload_real.php');

$finder = new Finder();
$finder->files()
    ->ignoreVCS(true)
    ->name('*.php')
    ->exclude('Tests')
    ->in('src')
    ->in('vendor/composer')
    ->in('vendor/doctrine')
    ->in('vendor/symfony/class-loader')
    ->in('vendor/symfony/console')
    ->in('vendor/symfony/yaml');

foreach ($finder as $file) {
    $phar->addFile($file->getPathName());
}

$finder = new Finder();
$finder->files()
    ->in('resources/migration-template/');

foreach ($finder as $file) {
    $phar->addFile($file->getPathName());
}

$phar->addFromString('index.php', '<?php require_once "src/create-application.php"; $app->run();');

$phar->setStub("#!/usr/bin/env php \n" . $phar->getStub());

$phar->stopBuffering();

// Make the file executable so it can be run without having to type `php` before it.
chmod($outputFile, 0555);
