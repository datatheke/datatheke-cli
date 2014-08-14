<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Finder\Finder;

$phar = new \Phar(__DIR__.'/datatheke.phar');

$finder = new Finder();
$finder->files()
    ->name('*.php')
    ->in(__DIR__.'/../src')
    ->in(__DIR__.'/../vendor')
;

foreach ($finder as $file) {
    $phar->addFile($file, substr($file, strlen(__DIR__.'/../')));
}

// Remove Shebang line
$content = file_get_contents(__DIR__.'/datatheke.php');
$content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
$phar->addFromString('bin/datatheke.php', $content);

$phar->setStub('#!/usr/bin/env php
<?php

Phar::mapPhar("datatheke.phar");
require "phar://datatheke.phar/bin/datatheke.php";

__HALT_COMPILER();
');