#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Datatheke\Cli\Command\Library;

$application = new Application();
$application->add(new Library());
$application->run();
