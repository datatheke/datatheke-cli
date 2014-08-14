<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Datatheke\Component\RestApi\Client;

class Printer extends Command
{
    protected function configure()
    {
        $this
            ->setName('list')
            // ->addArgument(
            //     'text',
            //     InputArgument::REQUIRED,
            //     'What do you want to print?'
            // )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $text = $input->getArgument('text');

        $client = new Client('iamluc', 'onlyforme');
        $libraries = $client->getLibraries();

        print_r($libraries);

        $l = key($libraries['items']);
        $library = $client->getLibrary($l);

        print_r($library);

        // $output->writeln('cool');
    }
}