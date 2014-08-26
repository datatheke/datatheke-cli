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
        $libraries = $this->container['client']->getLibraries();

        foreach ($libraries['items'] as $library) {
            $output->writeln(sprintf('<info>[%s]</info> %s', $library['id'], $library['name']));
        }
    }

    protected function listCollections(InputInterface $input, OutputInterface $output, $library)
    {
        $collections = $this->container['client']->getLibraryCollections($library);

        foreach ($collections['items'] as $collection) {
            $output->writeln(sprintf('<info>[%s]</info> %s', $collection['id'], $collection['name']));
        }
    }
}
