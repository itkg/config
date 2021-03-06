<?php

/*
 * This file is part of the Itkg\Core package.
 *
 * (c) Interakting - Business & Decision
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itkg\Tests\Core;

use Itkg\Core\Application;
use Itkg\Core\Config;
use Itkg\Core\ServiceContainer;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Itkg\Core\Command\Provider\ServiceCommandProvider;

/**
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class ServiceContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $config = new Config();
        $container = new ServiceContainer();
        $container->setConfig($config);
        $params = array(
            'dbname' => 'DBNAME',
            'user'   => 'USER',
            'password' => 'PWD',
            'host' => '',
            'driver' => 'oci8'
        );

        $config = new Configuration();
        $connection = DriverManager::getConnection($params, $config);
        $values = array('doctrine.connection' => $connection);

        $container->register(new ServiceCommandProvider(), $values);
        $this->assertEquals($connection, $container['doctrine.connection']);
    }

    public function testLoad()
    {
        $config = new Config();
        $app = new Application('DEV');
        $container = $this->getMock('Itkg\Core\ServiceContainer', array('loadConfig', 'loadApp'));
        $container->expects($this->once())->method('loadConfig')->with($config);
        $container->expects($this->once())->method('loadApp')->with($app);

        $container->setConfig($config);
        $container->setApp($app);
    }
}