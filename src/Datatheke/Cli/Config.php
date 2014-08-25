<?php

namespace Datatheke\Cli;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Yaml\Yaml;

class Config
{
    protected $path;
    protected $autoSave;
    protected $config;
    protected $accessor;

    public function __construct($path, $autoSave = true)
    {
        $this->path = $path;
        $this->autoSave = $autoSave;

        $this->config = $this->readConfig($this->path);
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public static function getInstance($path, $autoSave = true)
    {
        static $instances;

        if (null === $instances) {
            $instances = array();
        }

        if (!isset($instances[$path])) {
            $instances[$path] = new self($path, $autoSave);
        }

        return $instances[$path];
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function set($property, $value)
    {
        $this->accessor->setValue($this->config, $this->toArrayProperty($property), $value);

        return $this;
    }

    public function get($property)
    {
        return $this->accessor->getValue($this->config, $this->toArrayProperty($property));
    }

    private function toArrayProperty($property)
    {
        $paths = explode('.', $property);
        array_walk($paths, function (&$item, $key) { $item = '['.$item.']'; });

        return implode('', $paths);
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

    public function __destruct()
    {
        if ($this->autoSave) {
            $this->writeConfig($this->path, $this->config);
        }
    }
}
