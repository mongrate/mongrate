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
    ->notName('get-version.php')
    ->in('src')
    ->in('vendor/composer')
    ->in('vendor/doctrine')
    ->in('vendor/symfony/class-loader')
    ->in('vendor/symfony/console')
    ->in('vendor/symfony/yaml');

foreach ($finder as $file) {
    // Stripping whitespace from the file reduces the Phar file size by over 50% on first test
    // (from 1.5 MB to 679 KB).
    $minifiedPhp = php_strip_whitespace((string) $file->getPathName());

    $phar->addFromString($file->getPathName(), $minifiedPhp);
}

$finder = new Finder();
$finder->files()
    ->in('resources/migration-template/');

foreach ($finder as $file) {
    $phar->addFile($file->getPathName());
}

$version = require_once 'src/get-version.php';

$phar->addFromString('index.php', '
    <?php
    define("MONGRATE_VERSION", "' . $version . '");
    require_once "src/create-application.php";
    $app->run();
');

$phar->setStub("#!/usr/bin/env php \n" . $phar->getStub());

$phar->stopBuffering();

// Make the file executable so it can be run without having to type `php` before it.
chmod($outputFile, 0555);
