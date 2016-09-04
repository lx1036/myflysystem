<?php
/**
 * Created by PhpStorm.
 * User: liuxiang
 * Date: 9/4/16
 * Time: 9:50 PM
 */

namespace RightCapital\FlySystem\FlySystem\Adapter;

abstract class AbstractFtpAdapter extends AbstractAdapter
{
    /**
     * @var array $configurable
     */
    protected $configurable = [];
    /**
     * @var string $connection
     */
    protected $connection;
    /**
     * @var string $host
     */
    protected $host;
    /**
     * @var int $port
     */
    protected $port = 21;
    /**
     * @var string|null $username
     */
    protected $username;
    /**
     * @var string|null $password
     */
    protected $password;
    /**
     * @var bool $ssl
     */
    protected $ssl = false;
    /**
     * @var int $timeout
     */
    protected $timeout = 90;
    /**
     * @var bool $passive
     */
    protected $passive = true;
    /**
     * @var string $separator
     */
    protected $separator = '/';
    /**
     * @var string|null $root
     */
    protected $root;
    /**
     * @var int $perPublic
     */
    protected $permPublic = 0744;
    /**
     * @var int $permPrivate
     */
    protected $permPrivate = 0700;
    /**
     * @var string $systemType
     */
    protected $systemType;
    /**
     * @var bool $alternativeRecursion
     */
    protected $alternativeRecursion = false;

    public function __construct(array $config)
    {
        $this->setConfig($config);
    }

    public function setConfig(array $config)
    {
        foreach ($this->configurable as $setting) {
            if (!isset($config[$setting])) {
                continue;
            }

            $method = 'set' . ucfirst($setting);

            if (method_exists($this, $method)) {
                $this->$method($config[$setting]);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getConnection()
    {
        if (!$this->isConnected()) {
            $this->disconnect();
            $this->connect();
        }
        return $this->connection;
    }

    /**
     * Check if a connection is alive
     */
    abstract public function isConnected();

    /**
     * Close a connection
     */
    abstract public function disconnect();

    /**
     * Establish a connection
     */
    abstract public function connect();

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get the ftp port
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set the ftp port
     *
     * @param int|\RightCapital\FlySystem\FlySystem\Adapter\int $port
     *
     * @return $this
     */
    public function setPort(int $port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUsername()
    {
        return empty($this->username) ? 'anonymous' : $this->username;
    }

    /**
     * Set the ftp username
     *
     * @param null|string $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param null|string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int|\RightCapital\FlySystem\FlySystem\Adapter\int $timeout
     *
     * @return $this
     */
    public function setTimeout(int $timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set the root folder to work from
     *
     * @param null|string $root
     *
     * @return $this
     */
    public function setRoot($root)
    {
        $this->root = rtrim($root, '\\/') . $this->separator;

        return $this;
    }

    /**
     * @return int
     */
    public function getPermPublic()
    {
        return $this->permPublic;
    }

    /**
     * @param int $permPublic
     */
    public function setPermPublic($permPublic)
    {
        $this->permPublic = $permPublic;
    }

    /**
     * @return int
     */
    public function getPermPrivate()
    {
        return $this->permPrivate;
    }

    /**
     * @param int $permPrivate
     */
    public function setPermPrivate($permPrivate)
    {
        $this->permPrivate = $permPrivate;
    }

    /**
     * @return string
     */
    public function getSystemType()
    {
        return $this->systemType;
    }

    /**
     * @param string $systemType
     */
    public function setSystemType($systemType)
    {
        $this->systemType = $systemType;
    }

    public function listContents($directory = '', $recursive = false)
    {
        return $this->listDirectoryContents($directory, $recursive);
    }

    abstract protected function listDirectoryContents($directory = '', $recursive = false);
}
