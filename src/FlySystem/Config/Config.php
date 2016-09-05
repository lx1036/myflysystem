<?php
/**
 * Created by PhpStorm.
 * User: liuxiang
 * Date: 9/4/16
 * Time: 11:03 PM
 */

namespace RightCapital\FlySystem\Config;

class Config
{
    /**
     * @var array
     */
    protected $settings = [];
    /**
     * @var Config
     */
    protected $fallback;

    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
    }

    /**
     * Set the fallback.
     *
     * @param \RightCapital\FlySystem\Config\Config $fallback
     *
     * @return $this
     */
    public function setFallback(Config $fallback)
    {
        $this->fallback = $fallback;

        return $this;
    }

    /**
     * Set a setting.
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $this->settings[$key] = $value;

        return $this;
    }

    public function get($key, $default = null)
    {
        if (!array_key_exists($key, $this->settings)) {
            return $this->getDefault($key, $default);
        }

        return $this->settings[$key];
    }

    protected function getDefault($key, $default)
    {
        if (!$this->fallback) {
            return $default;
        }

        return $this->fallback->get($key, $default);
    }
}