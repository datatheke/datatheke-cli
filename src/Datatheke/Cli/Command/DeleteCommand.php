<?php

namespace Datatheke\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteCommand extends AbstractBaseCommand
{
    protected function configure()
    {
        $this
            ->setName('delete')
            ->setAliases(['remove'])
            ->setDescription('Delete data')
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'Path to data'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $paths = explode('/', $input->getArgument('path'));

        if (1 === count($paths)) {
            $this->deleteLibrary($input, $output, $paths[0]);
        } elseif (2 === count($paths)) {
            $this->deleteCollection($input, $output, $paths[1]);
        } elseif (3 === count($paths)) {
            $this->deleteItem($input, $output, $paths[1], $paths[2]);
        } else {
            throw new \Exception('Invalid path');
        }
    }

    protected function deleteLibrary(InputInterface $input, OutputInterface $output, $libraryId)
    {
        $this->container['client']->deleteLibrary($libraryId);
    }

    protected function deleteCollection(InputInterface $input, OutputInterface $output, $collectionId)
    {
        $this->container['client']->deleteCollection($collectionId);
    }

    protected function deleteItem(InputInterface $input, OutputInterface $output, $collectionId, $itemId)
    {
        $this->container['client']->deleteItem($collectionId, $itemId);
    }
}
