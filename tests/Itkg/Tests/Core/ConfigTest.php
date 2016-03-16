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

/**
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Application
     */
    protected $application;

    protected function setUp()
    {
        $this->application = new Application();

        $this->config = new Config(array(TEST_BASE_DIR.'/data/config/config.yml'));

        $this->application->setConfig($this->config);
    }

    public function testGetSetValue()
    {
        // Explicit isset
        $this->assertFalse($this->config->has('nonexistent'));
        $this->assertTrue($this->config->has('bar'));

        // ArrayAccess isset
        $this->assertFalse(isset($this->config['you']));
        $this->assertTrue(isset($this->config['foo']));

        // Explicit getter
        $this->assertEquals($this->config->get('bar'), 'foo');
        // ArrayAccess getter
        $this->assertEquals($this->config->get('bar'), $this->config['bar']);

        // ArrayAccess setter
        $this->config['new'] = 'value';
        $this->assertEquals($this->config->get('new'), 'value', 'ArrayAccess set does not set value correctly');

        // Multi level ArrayAccess setter
        $this->config['ONE']['TWO'] = 'three';
        $this->assertEquals('three', $this->config['ONE']['TWO']);

        // Explicit setter
        $this->config->set('anotherOne', 'dummy');
        $this->assertEquals($this->config->get('anotherOne'), 'dummy', 'config::set() set does not set value correctly');

        // ArrayAccess on non existing key returns null
        $this->assertNull($this->config['WTF']);

        // Get all values
        $this->assertInternalType('array', $this->config->all());

        // Merge array to config
        $this->config->mergeParams(array('fieldToMerge' => 'mergedField'));
        $this->assertEquals($this->config->get('fieldToMerge'), 'mergedField');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNoValueException()
    {
        $this->config->offsetUnset('foo');
        $this->config->get('foo');
    }

}
