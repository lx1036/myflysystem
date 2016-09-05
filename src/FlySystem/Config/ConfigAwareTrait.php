<?php

namespace RightCapital\FlySystem\Config;

use RightCapital\FlySystem\Support\Util;

trait ConfigAwareTrait
{
    /**
     * @var Config $config
     */
    protected $config;

    /**
     * Get the field config.
     * 
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set the field config.
     * 
     * @param $config
     */
    protected function setConfig($config)
    {
        $this->config = $config ? Util::ensureConfig($config) : new Config();
    }

    public function prepareConfig(array $config)
    {
        $config = new Config($config);
        $config->setFallback($this->getConfig());

        return $config;
    }
}