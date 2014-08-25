<?php

namespace Datatheke\Cli;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Pimple\Container;

class Application extends BaseApplication
{
    const VERSION = '0.1.0';

    public function __construct()
    {
        parent::__construct('Datatheke-cli', self::VERSION);
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        foreach ($this->all() as $command) {
            if ($command instanceof ContainerAwareInterface) {
                $command->setContainer($container);
            }
        }

        return parent::doRun($input, $output);
    }

    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = new Command\ConfigCommand();
        $commands[] = new Command\LibraryCommand();

        return $commands;
    }

    protected function getContainer()
    {
        static $container;

        if (null === $container) {
            $container = new Container();
            $container['config_path'] = getenv('HOME').'/.datatheke';
            $container['config'] = function ($c) {
                return new Config($c['config_path']);
            };
        }

        return $container;
    }
}
