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
            ->addArgument(
                'option',
                InputArgument::OPTIONAL,
                'The name of the option'
            )
            ->addArgument(
                'value',
                InputArgument::OPTIONAL,
                'The value of the option'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $option = $input->getArgument('option');
        $value = $input->getArgument('value');

        if (!$option && !$value) {
            $output->writeln(Yaml::dump($this->config->getConfig()));

            return;
        }

        $this->config->set($option, $value);
    }
}
