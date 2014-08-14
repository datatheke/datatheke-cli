<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Datatheke\Component\RestApi\Client;

class Library extends Command
{
    protected function configure()
    {
        $this
            ->setName('library')
            ->addArgument(
                'library',
                InputArgument::OPTIONAL,
                'Set the library id to see library collections'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new Client('iamluc', 'onlyforme');

        if (null !== ($library = $input->getArgument('library'))) {
            $this->listCollections($input, $output, $client, $library);
        } else {
            $this->listLibraries($input, $output, $client);
        }
    }

    protected function listLibraries(InputInterface $input, OutputInterface $output, Client $client)
    {
        $libraries = $client->getLibraries();

        foreach ($libraries['items'] as $library) {
            $output->writeln(sprintf('[%s] %s', $library['id'], $library['name']));
        }
    }

    protected function listCollections(InputInterface $input, OutputInterface $output, Client $client, $library)
    {
        $collections = $client->getLibraryCollections($library);

        foreach ($collections['items'] as $collection) {
            $output->writeln(sprintf('[%s] %s', $collection['id'], $collection['name']));
        }
    }
}