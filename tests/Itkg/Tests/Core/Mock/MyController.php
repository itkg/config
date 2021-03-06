<?php

/*
 * This file is part of the Itkg\Core package.
 *
 * (c) Interakting - Business & Decision
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itkg\Tests\Core\Mock;


use Itkg\Core\ServiceContainer;
use Symfony\Component\HttpFoundation\Request;

class MyController
{
    private $container;
    private $path;
    private $request;

    public function indexAction()
    {

    }

    public function setContainer(ServiceContainer $container)
    {
        $this->container = $container;
        return $this;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    public function setPath($path = '')
    {
        $this->path = $path;

        return $this;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function getRequest()
    {
        return $this->request;
    }

} 