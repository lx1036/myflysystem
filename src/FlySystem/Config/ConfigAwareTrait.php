<?php

namespace RightCapital\FlySystem\FlySystem\Config;

use RightCapital\FlySystem\FlySystem\Util;

trait ConfigAwareTrait
{
    /**
     * @var Config $config
     */
    protected $config;

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
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