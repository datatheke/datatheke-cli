<?php

namespace Datatheke\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class BrowseCommand extends AbstractBaseCommand
{
    protected function configure()
    {
        $this
            ->setName('browse')
            ->setDescription('Browse data repository')
            ->addArgument(
                'path',
                InputArgument::OPTIONAL,
                'Path to data'
            )
            ->addOption(
                'page',
                'p',
                InputOption::VALUE_REQUIRED,
                'Page number',
                1
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');
        $page = $input->getOption('page');

        if (null === $path) {
            $this->browseLibraries($input, $output, $page);
        } else {
            $paths = explode('/', $path);

            if (1 === count($paths)) {
                $this->browseCollections($input, $output, $paths[0], $page);
            } elseif (2 === count($paths)) {
                $this->browseItems($input, $output, $paths[1], $page);
            } elseif (3 === count($paths)) {
                $this->viewItem($input, $output, $paths[1], $paths[2]);
            } else {
                throw new \Exception('Invalid path');
            }
        }
    }

    protected function browseLibraries(InputInterface $input, OutputInterface $output, $page)
    {
        $libraries = $this->container['client']->getLibraries($page);

        foreach ($libraries['items'] as $library) {
            $output->writeln(sprintf('<info>[%s]</info> %s', $library['id'], $library['name']));
        }
    }

    protected function browseCollections(InputInterface $input, OutputInterface $output, $libraryId, $page)
    {
        $collections = $this->container['client']->getLibraryCollections($libraryId, $page);

        foreach ($collections['items'] as $collection) {
            $output->writeln(sprintf('<info>[%s]</info> %s', $collection['id'], $collection['name']));
        }
    }

    protected function browseItems(InputInterface $input, OutputInterface $output, $collectionId, $page)
    {
        $items = $this->container['client']->getCollectionItems($collectionId, $page);

        foreach ($items['items'] as $item) {
            $output->writeln(sprintf('<info>[%s]</info> %s', $item['id'], json_encode($item)));
        }
    }

    protected function viewItem(InputInterface $input, OutputInterface $output, $collectionId, $itemId)
    {
        $item = $this->container['client']->getItem($collectionId, $itemId);

        $output->writeln(Yaml::dump($item));
    }
}
