<?php

namespace Datatheke\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

use Datatheke\Component\Api\Client;
use Datatheke\Cli\Config;

abstract class AbstractBaseCommand extends Command
{
    protected $config;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $configFile = getenv('HOME').'/.datatheke';
        $this->config = Config::getInstance($configFile);
    }

    protected function getClient(InputInterface $input, OutputInterface $output)
    {
        static $client;

        if ($client) {
            return $client;
        }

        $url = $this->config->get('url');
        $token = $this->config->get('token');

        if ($token) {
            $client = new Client($token['access_token'], $token['refresh_token'], $token['expires_at'], $url);
        } else {
            $helper = $this->getHelper('question');

            $question = new Question('Username: ');
            $username = $helper->ask($input, $output, $question);

            $question = new Question('Password: ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $password = $helper->ask($input, $output, $question);

            $client = Client::createFromUserCredentials($username, $password, $url);

            $this->config->set('token', $client->getToken());
        }

        return $client;
    }
}
