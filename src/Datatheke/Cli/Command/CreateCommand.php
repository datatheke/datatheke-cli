<?php

namespace Datatheke\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

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

        $helper = $this->getHelperSet()->get('question');

        $name = $helper->ask($input, $output, new Question('Name: '));
        $description = $helper->ask($input, $output, new Question('Description: '));

        $id = $this->container['client']->createLibrary($name, $description);
        $output->writeln(sprintf('<info>%s</info>', $id));
    }

    protected function createCollection(InputInterface $input, OutputInterface $output, $library)
    {
        $output->writeln('<info>Creating collection</info>');

        $helper = $this->getHelperSet()->get('question');

        $name = $helper->ask($input, $output, new Question('Name: '));
        $description = $helper->ask($input, $output, new Question('Description: '));

        $fields = array();
        while (true) {
            $label = $helper->ask($input, $output, new Question('Field label: '));
            if (null === $label) {
                break;
            }

            $type = $helper->ask($input, $output, new Question('Field type: '));
            $fields[] = ['label' => $label, 'type' => $type];
        }

        if (count($fields) < 1) {
            throw new \Exception('At least one field is required');
        }

        $id = $this->container['client']->createCollection($library, $name, $description, $fields);
        $output->writeln(sprintf('<info>%s</info>', $id));
    }

    protected function createItem(InputInterface $input, OutputInterface $output, $collection)
    {
        $output->writeln('<info>Creating item</info>');

        $helper = $this->getHelperSet()->get('question');

        $definition = $this->container['client']->getCollection($collection);
        $values = array();
        foreach ($definition['fields'] as $field) {
            $value = $helper->ask($input, $output, new Question(sprintf('%s (%s): ', $field['label'], $field['type'])));

            $values['_'.$field['id']] = $value;
        }

        $id = $this->container['client']->createItem($collection, $values);
        $output->writeln(sprintf('<info>%s</info>', $id));
    }
}
