<?php

namespace Datatheke\Cli;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Question\Question;

use Datatheke\Api\Client;
use Datatheke\Api\Event\CredentialsEvent;
use Datatheke\Api\Event\AccessTokenUpdatedEvent;

class ClientFactory
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function createClient(InputInterface $input, OutputInterface $output, HelperSet $helperSet)
    {
        $helper = $helperSet->get('question');
        $config = $this->config;

        $getCredentials = function (CredentialsEvent $event) use ($input, $output, $helper) {
            $question = new Question('Username: ');
            $username = $helper->ask($input, $output, $question);

            $question = new Question('Password: ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $password = $helper->ask($input, $output, $question);

            $event->setCredentials($username, $password);
        };

        $updateConfig = function (AccessTokenUpdatedEvent $event) use ($config) {
            $config->set('token', $event->getAccessToken()->toArray());
        };

        $client = new Client();
        $client->addListener('datatheke_client.credentials', $getCredentials);
        $client->addListener('datatheke_client.access_token_updated', $updateConfig);

        if ($token = $config->get('token')) {
            $client->setTokenData($token['access_token'], $token['refresh_token'], $token['expires_at']);
        }

        if ($url = $config->get('http.url')) {
            $client->setUrl($url);
        }

        if ($httpConfig = $config->get('http.config')) {
            $client->setConfig($httpConfig);
        }

        return $client;
    }
}
