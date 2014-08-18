<?php

namespace Datatheke\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

use Symfony\Component\Yaml\Yaml;
// use Symfony\Component\Yaml\Exception\ParseException;
// use Symfony\Component\Yaml\Exception\DumpException;

use Datatheke\Component\RestApi\Client;

abstract class AbstractBaseCommand extends Command
{
    protected $configFile;
    protected $url;

    public function __construct($name = null, $url = null)
    {
        parent::__construct($name);

        $this->configFile = getenv('HOME').'/.datatheke';

        // $this->url = $url;
        // $this->url = 'http://datatheke.local:8080/app_dev.php/';
        // $this->url = 'http://0.0.0.0/datatheke/app_dev.php/';
        $this->url = 'https://www.datatheke.com/';
    }

    protected function getClient(InputInterface $input, OutputInterface $output)
    {
        static $client;

        if ($client) {
            return $client;
        }

        if ($token = $this->readToken()) {
            $client = new Client($token['access_token'], $token['refresh_token'], $token['expires_in'], $this->url);
        } else {
            $helper = $this->getHelper('question');

            $question = new Question('Username: ');
            $username = $helper->ask($input, $output, $question);

            $question = new Question('Password: ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $password = $helper->ask($input, $output, $question);

            $client = Client::createFromUserCredentials($username, $password, $this->url);

            $this->storeToken($client->getToken());
        }

        return $client;
    }

    private function readToken()
    {
        if (!file_exists($this->configFile)) {
            return;
        }

        return Yaml::parse(file_get_contents($this->configFile));
    }

    private function storeToken($token)
    {
        file_put_contents($this->configFile, Yaml::dump($token));
        chmod($this->configFile, 0600);
    }
}