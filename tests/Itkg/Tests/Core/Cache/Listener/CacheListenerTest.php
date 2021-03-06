<?php

/*
 * This file is part of the Itkg\Core package.
 *
 * (c) Interakting - Business & Decision
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class CacheListenerTest extends \PHPUnit_Framework_TestCase
{

    public function testFetchEntityFromCache()
    {
        $stub = $this->getMock('Itkg\Core\Cache\Adapter\Redis', array('get', 'set'), array(array()));
        $stub->expects($this->once())->method('get')->will($this->returnValue(array('YEAH')));
        $dispatcherStub = $this->getMock('\Symfony\Component\EventDispatcher\EventDispatcher');
        $dispatcherStub->expects($this->once())->method('dispatch')->will($this->returnValue(array('cache.load', array('YEAH'))));
        $entity = new \Itkg\Core\Cache\CacheableData('my hash', 86400, array());

        $listener = new \Itkg\Core\Cache\Listener\CacheListener($stub, $dispatcherStub);
        $listener->fetchEntityFromCache(new \Itkg\Core\Event\EntityLoadEvent($entity));

        $this->assertEquals($entity->getDataForCache(), array('YEAH'));
        $this->assertTrue($entity->isLoaded());
    }

    public function testSetCacheEntity()
    {
        $stub = $this->getMock('Itkg\Core\Cache\Adapter\Redis', array('get', 'set'), array(array('YEAH')));
        $stub->expects($this->once())->method('set')->will($this->returnValue(array('YEAH')));
        $dispatcherStub = $this->getMock('\Symfony\Component\EventDispatcher\EventDispatcher');
        $dispatcherStub->expects($this->once())->method('dispatch')->will($this->returnValue(array('cache.set', array('YEAH'))));
        $entity = new \Itkg\Core\Cache\CacheableData('my hash', 86400, array());

        $listener = new \Itkg\Core\Cache\Listener\CacheListener($stub, $dispatcherStub);
        $listener->setCacheForEntity(new \Itkg\Core\Event\EntityLoadEvent($entity));
    }

    public function testPurgeCache()
    {
        $cacheMock = $this->getMockBuilder('\Itkg\Core\Cache\Adapter\Redis')
            ->disableOriginalConstructor()
            ->setMethods(array('removeAll'))
            ->getMock();

        $cacheMock->expects($this->once())
            ->method('removeAll');

        $dispatchMock =  $this->getMock('\Symfony\Component\EventDispatcher\EventDispatcher');

        $listener = new \Itkg\Core\Cache\Listener\CacheListener($cacheMock, $dispatchMock);
        $listener->purgeCache();
    }
}
