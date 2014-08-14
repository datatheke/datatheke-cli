#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Command\Printer;

$application = new Application();
$application->add(new Printer());
$application->run();
