<?php

namespace Datatheke\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCommand extends AbstractBaseCommand
{
    protected function configure()
    {
        $this
            ->setName('create')
            ->setDescription('Create data')
            ->addArgument(
                'path',
                InputArgument::OPTIONAL,
                'Path to data'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');

        if (null === $path) {
            $this->createLibrary($input, $output);
        } else {
            $paths = explode('/', $path);

            if (1 === count($paths)) {
                $this->createCollection($input, $output, $paths[0]);
            } elseif (2 === count($paths)) {
                $this->createItem($input, $output, $paths[1]);
            } else {
                throw new \Exception('Invalid path');
            }
        }
    }

    protected function createLibrary(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Creating library</info>');

        $name = $this->container['questioner']->ask('Name');
        $description = $this->container['questioner']->ask('Description');

        $id = $this->container['client']->createLibrary($name, $description);
        $output->writeln(sprintf('<info>%s</info>', $id));
    }

    protected function createCollection(InputInterface $input, OutputInterface $output, $libraryId)
    {
        $output->writeln('<info>Creating collection</info>');

        $name = $this->container['questioner']->ask('Name');
        $description = $this->container['questioner']->ask('Description');

        $fields = array();
        while (true) {
            $label = $this->container['questioner']->ask('Field label');
            if (null === $label) {
                break;
            }

            $type = $this->container['questioner']->ask('Field type');
            $fields[] = ['label' => $label, 'type' => $type];
        }

        if (count($fields) < 1) {
            throw new \Exception('At least one field is required');
        }

        $id = $this->container['client']->createCollection($libraryId, $name, $description, $fields);
        $output->writeln(sprintf('<info>%s</info>', $id));
    }

    protected function createItem(InputInterface $input, OutputInterface $output, $collectionId)
    {
        $output->writeln('<info>Creating item</info>');

        $definition = $this->container['client']->getCollection($collectionId);
        $values = array();
        foreach ($definition['fields'] as $field) {
            $value = $this->container['questioner']->ask(sprintf('%s <info>(%s)</info>', $field['label'], $field['type']));
            $values['_'.$field['id']] = $value;
        }

        $id = $this->container['client']->createItem($collectionId, $values);
        $output->writeln(sprintf('<info>%s</info>', $id));
    }
}
