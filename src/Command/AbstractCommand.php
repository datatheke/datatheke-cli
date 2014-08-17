<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

use Datatheke\Component\RestApi\Client;

abstract class AbstractCommand extends Command
{
    protected $configFile;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->configFile = getenv('HOME').'/.datatheke';
    }

    protected function getClient(InputInterface $input, OutputInterface $output)
    {
        static $client;

        if ($client) {
            return $client;
        }

        if ($token = $this->readToken()) {
            $client = Client::createWithToken($token);
        } else {
            $helper = $this->getHelper('question');

            $question = new Question('Username: ');
            $username = $helper->ask($input, $output, $question);

            $question = new Question('Password: ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $password = $helper->ask($input, $output, $question);

            $client = new Client($username, $password);
            $client->connect();

            $this->storeToken($client->token);
        }

        return $client;
    }

    private function readToken()
    {
        if (!file_exists($this->configFile)) {
            return;
        }

        return file_get_contents($this->configFile);
    }

    private function storeToken($token)
    {
        file_put_contents($this->configFile, $token);
        chmod($this->configFile, 0600);
    }
}
