<?php

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

    /**
     * Config constructor.
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
    }

    /**
     * Set the fallback to "Update" the config.
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

    /**
     * Get the value from the settings attribute.
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (!array_key_exists($key, $this->settings)) {
            return $this->getDefault($key, $default);
        }

        return $this->settings[$key];
    }

    /**
     * @param $key
     * @param $default
     *
     * @return mixed
     */
    protected function getDefault($key, $default)
    {
        if (!$this->fallback) {
            return $default;
        }

        return $this->fallback->get($key, $default);
    }

    /**
     * Check the key if exists in the settings attribute.
     *
     * @param $key
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->settings);
    }
}