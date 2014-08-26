<?php

namespace Datatheke\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CollectionCommand extends AbstractBaseCommand
{
    protected function configure()
    {
        $this
            ->setName('collection')
            ->setDescription('Interact with collections')
            ->addArgument(
                'collection',
                InputArgument::OPTIONAL,
                'Set a collection identifier to see items in that collection'
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
        $page = $input->getOption('page');

        if (null !== ($collection = $input->getArgument('collection'))) {
            $this->listItems($input, $output, $collection, $page);
        }
    }

    protected function listItems(InputInterface $input, OutputInterface $output, $collection, $page)
    {
        $items = $this->container['client']->getCollectionItems($collection, $page);

        foreach ($items['items'] as $item) {
            $output->writeln(sprintf('<info>[%s]</info> %s', $item['id'], json_encode($item)));
        }
    }
}
