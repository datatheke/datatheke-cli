<?php

namespace Datatheke\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

use Pimple\Container;

use Datatheke\Component\Api\Client;
use Datatheke\Cli\ContainerAwareInterface;

abstract class AbstractBaseCommand extends Command implements ContainerAwareInterface
{
    protected $container;
    protected $client;

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    protected function getClient(InputInterface $input, OutputInterface $output)
    {
        if ($this->client) {
            return $this->client;
        }

        $helper = $this->getHelper('question');
        $setCredentials = function () use ($input, $output, $helper) {
            $question = new Question('Username: ');
            $username = $helper->ask($input, $output, $question);

            $question = new Question('Password: ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $password = $helper->ask($input, $output, $question);

            return [$username, $password];
        };

        $this->client = new Client();
        $this->client->setCredentials($setCredentials);

        if ($token = $this->container['config']->get('token')) {
            $this->client->setTokenData($token['access_token'], $token['refresh_token'], $token['expires_at']);
        }

        if ($url = $this->container['config']->get('http.url')) {
            $this->client->setUrl($url);
        }

        if ($url = $this->container['config']->get('http.config')) {
            $this->client->setConfig($config);
        }

        return $this->client;
    }

    public function __destruct()
    {
        if (null !== $this->client) {
            $this->container['config']->set('token', $this->client->getLastToken());
        }
    }
}
