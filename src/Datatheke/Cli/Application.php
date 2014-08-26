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
        $container = $this->createContainer();
        $container['input'] = $input;
        $container['output'] = $output;
        $container['helper_set'] = $this->getHelperSet();

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

    protected function createContainer()
    {
        $container = new Container();

        $container['config_path'] = getenv('HOME').'/.datatheke';
        $container['config'] = function ($c) {
            return new Config($c['config_path']);
        };
        $container['client_factory'] = function ($c) {
            return new ClientFactory($c['config']);
        };
        $container['client'] = function ($c) {
            return $c['client_factory']->createClient($c['input'], $c['output'], $c['helper_set']);
        };

        return $container;
    }
}
