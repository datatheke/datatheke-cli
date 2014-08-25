<?php

namespace Datatheke\Cli;

use Pimple\Container;

interface ContainerAwareInterface
{
    public function setContainer(Container $container);
}
