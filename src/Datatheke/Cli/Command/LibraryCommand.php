<?php

namespace Datatheke\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
                'Set a library identifier to see collections in that library'
            )
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Name of the library you want to create',
                'New library'
            )
            ->addArgument(
                'description',
                InputArgument::OPTIONAL,
                'Description of the library you want to create'
            )
            ->addOption(
                'page',
                'p',
                InputOption::VALUE_REQUIRED,
                'Page number',
                1
            )
            ->addOption(
                'remove',
                'r',
                InputOption::VALUE_NONE,
                'Remove library'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $page = $input->getOption('page');

        if (null !== ($library = $input->getArgument('library'))) {

            if ($input->getOption('remove')) {
                $this->deleteLibrary($input, $output, $library);

                return;
            }

            if ('create' === $library) {
                $this->createLibrary($input, $output);

                return;
            }

            $this->listCollections($input, $output, $library, $page);
        } else {
            $this->listLibraries($input, $output, $page);
        }
    }

    protected function deleteLibrary(InputInterface $input, OutputInterface $output, $library)
    {
        $this->container['client']->deleteLibrary($library);
    }

    protected function createLibrary(InputInterface $input, OutputInterface $output)
    {
        $id = $this->container['client']->createLibrary($input->getArgument('name'), $input->getArgument('description'));

        $output->writeln(sprintf('<info>%s</info>', $id));
    }

    protected function listLibraries(InputInterface $input, OutputInterface $output, $page)
    {
        $libraries = $this->container['client']->getLibraries($page);

        foreach ($libraries['items'] as $library) {
            $output->writeln(sprintf('<info>[%s]</info> %s', $library['id'], $library['name']));
        }
    }

    protected function listCollections(InputInterface $input, OutputInterface $output, $library, $page)
    {
        $collections = $this->container['client']->getLibraryCollections($library, $page);

        foreach ($collections['items'] as $collection) {
            $output->writeln(sprintf('<info>[%s]</info> %s', $collection['id'], $collection['name']));
        }
    }
}
