<?php

namespace Datatheke\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LibraryCommand extends AbstractBaseCommand
{
    protected function configure()
    {
        $this
            ->setName('library')
            ->setDescription('Interact with libraries')
            ->addArgument(
                'library',
                InputArgument::OPTIONAL,
                'Set the library id to see library collections'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null !== ($library = $input->getArgument('library'))) {
            $this->listCollections($input, $output, $library);
        } else {
            $this->listLibraries($input, $output);
        }
    }

    protected function listLibraries(InputInterface $input, OutputInterface $output)
    {
        $libraries = $this->getClient($input, $output)->getLibraries();

        foreach ($libraries['items'] as $library) {
            $output->writeln(sprintf('[%s] %s', $library['id'], $library['name']));
        }
    }

    protected function listCollections(InputInterface $input, OutputInterface $output, $library)
    {
        $collections = $this->getClient($input, $output)->getLibraryCollections($library);

        foreach ($collections['items'] as $collection) {
            $output->writeln(sprintf('[%s] %s', $collection['id'], $collection['name']));
        }
    }
}
