<?php

namespace Datatheke\Cli\Command;

use Symfony\Component\Console\Command\Command;

use Pimple\Container;

use Datatheke\Cli\ContainerAwareInterface;

abstract class AbstractBaseCommand extends Command implements ContainerAwareInterface
{
    protected $container;

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}
