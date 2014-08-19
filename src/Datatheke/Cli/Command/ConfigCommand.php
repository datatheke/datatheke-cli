<?php

namespace Datatheke\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class ConfigCommand extends AbstractBaseCommand
{
    protected function configure()
    {
        $this
            ->setName('config')
            ->setDescription('Get and set configuration')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(Yaml::dump($this->config->getConfig()));
    }
}
