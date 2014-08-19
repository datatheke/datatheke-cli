<?php

namespace Datatheke\Cli;

use Symfony\Component\Yaml\Yaml;

class Config
{
    protected $path;
    protected $config;

    public function __construct($path)
    {
        $this->path = $path;
        $this->config = $this->readConfig($this->path);
    }

    public function __destruct()
    {
        $this->writeConfig($this->path, $this->config);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getToken()
    {
        if (isset($this->config['token'])) {
            return $this->config['token'];
        }

        return null;
    }

    public function setToken($token)
    {
        $this->config['token'] = $token;
    }

    public function getUrl()
    {
        // return 'http://datatheke.local:8080/app_dev.php/';
        // return 'http://0.0.0.0/datatheke/app_dev.php/';
        // return 'https://www.datatheke.com/';

        if (isset($this->config['http']['url'])) {
            return $this->config['http']['url'];
        }

        return null;
    }

    public function setUrl($url)
    {
        $this->config['http']['url'] = $url;
    }

    private function readConfig($path)
    {
        if (file_exists($path)) {
            return Yaml::parse(file_get_contents($this->path));
        }

        return [];
    }

    private function writeConfig($path, $config)
    {
        file_put_contents($path, Yaml::dump($config));
        chmod($path, 0600);
    }
}
