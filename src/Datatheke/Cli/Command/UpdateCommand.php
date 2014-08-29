<?php

namespace Datatheke\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends AbstractBaseCommand
{
    protected function configure()
    {
        $this
            ->setName('update')
            ->setDescription('Update data')
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
            $this->updateLibrary($input, $output, $paths[0]);
        } elseif (2 === count($paths)) {
            $this->updateCollection($input, $output, $paths[1]);
        } elseif (3 === count($paths)) {
            $this->updateItem($input, $output, $paths[1], $paths[2]);
        } else {
            throw new \Exception('Invalid path');
        }
    }

    protected function updateLibrary(InputInterface $input, OutputInterface $output, $libraryId)
    {
        $output->writeln('<info>Updating library</info>');

        $library = $this->container['client']->getLibrary($libraryId);

        $name = $this->container['questioner']->ask('Name', $library['name']);
        $description = $this->container['questioner']->ask('Description', $library['description']);
        $public = $this->container['questioner']->askConfirmation('public', !$library['private']);
        if ($public) {
            $collaborative = $this->container['questioner']->askConfirmation('collaborative', $library['collaborative']);
        } else {
            $collaborative = false;
        }

        $this->container['client']->updateLibrary($library['id'], $name, $description, $public, $collaborative);
    }

    protected function updateCollection(InputInterface $input, OutputInterface $output, $collectionId)
    {
        $output->writeln('<info>Updating collection</info>');

        $definition = $this->container['client']->getCollection($collectionId);

        $name = $this->container['questioner']->ask('Name', $definition['name']);
        $description = $this->container['questioner']->ask('Description', $definition['description']);

        $fields = array();
        foreach ($definition['fields'] as $key => $field) {
            $fields[$key]['label'] = $this->container['questioner']->ask('Field label', $field['label']);
        }

        while (true) {
            $label = $this->container['questioner']->ask('New Field label');
            if (null === $label) {
                break;
            }

            $type =  $this->container['questioner']->ask('New Field type');
            $fields[] = ['label' => $label, 'type' => $type];
        }

        if (count($fields) < 1) {
            throw new \Exception('At least one field is required');
        }

        $this->container['client']->updateCollection($collectionId, $name, $description, $fields);
    }

    protected function updateItem(InputInterface $input, OutputInterface $output, $collectionId, $itemId)
    {
        $output->writeln('<info>Updating item</info>');

        $definition = $this->container['client']->getCollection($collectionId);
        $item = $this->container['client']->getItem($collectionId, $itemId);
        $values = array();
        foreach ($definition['fields'] as $field) {
            $value = $this->container['questioner']->ask(sprintf('%s (%s)', $field['label'], $field['type']), $item['_'.$field['id']]);
            $values['_'.$field['id']] = $value;
        }

        $this->container['client']->updateItem($collectionId, $itemId, $values);
    }
}
