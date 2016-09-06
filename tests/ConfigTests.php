<?php

namespace RightCapital\FlySystem\Test;

use RightCapital\FlySystem\Config\Config;

class ConfigTests extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var Config
     */
    protected $fallback;

    public function setUp()
    {
        $this->config   = new Config();
        $this->fallback = new Config([
            'fallback_setting' => 'fallback_value',
        ]);
    }

    public function testSetAndGet()
    {
        $this->assertFalse($this->config->has('setting'));
        $this->assertNull($this->config->get('setting'));
        $this->config->set('setting', 'value');
        $this->assertEquals('value', $this->config->get('setting'));
    }

    public function testFallback()
    {
        $this->config->setFallback($this->fallback);
        $this->assertEquals('fallback_value', $this->config->get('fallback_setting'));
    }
}