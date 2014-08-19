<?php

namespace Datatheke\Cli;

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    const VERSION = '0.1.0';
    
    public function __construct()
    {
        parent::__construct('Datatheke-cli', self::VERSION);
    }
    
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = new Command\ConfigCommand();
        $commands[] = new Command\LibraryCommand();

        return $commands;
    }    
}