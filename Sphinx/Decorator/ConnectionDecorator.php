<?php

namespace Chebur\SphinxBundle\Sphinx\Decorator;

use Chebur\SphinxBundle\Profiler\Logger;
use Chebur\SphinxBundle\Sphinx\Connection\MysqliConnection;
use Chebur\SphinxBundle\Sphinx\Connection\PdoConnection;
use Foolz\SphinxQL\Drivers\ConnectionInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class ConnectionDecorator implements ConnectionInterface
{
    const DRIVER_PDO    = 'pdo';
    const DRIVER_MYSQLI = 'mysqli';

    const DEFAULT_HOST     = 'localhost';
    const DEFAULT_PORT     = 9306;
    const DEFAULT_PORT_API = 9312;

    const DEFAULT_DRIVER = 'pdo';

    /**
     * @var MysqliConnection|PdoConnection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var int
     */
    protected $portApi;

    /**
     * @param string $name
     * @param Logger $logger
     * @param string $type
     * @param string $host
     * @param int    $port
     * @param int    $portApi
     * @todo добавить возможность передавать параметры подключения
     */
    public function __construct($name, Logger $logger, $type = self::DEFAULT_DRIVER, $host = self::DEFAULT_HOST, $port = self::DEFAULT_PORT, $portApi = self::DEFAULT_PORT_API)
    {
        $this->name = $name;

        $this->logger = $logger;

        switch ($type) {
            case self::DRIVER_MYSQLI:
                $driver = new MysqliConnection();
                break;
            case self::DRIVER_PDO:
                $driver = new PdoConnection();
                break;
            default:
                throw new InvalidArgumentException(
                    'Invalid driver type. "'.$type.'" not in list ('.self::DRIVER_MYSQLI.', '.self::DRIVER_PDO.')'
                );
                break;
        }
        $driver->setParams([
            'host' => $host,
            'port' => $port,
        ]);
        $this->connection = $driver;

        $this->port    = $port;
        $this->host    = $host;
        $this->portApi = $portApi;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getHost() : string
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort() : int
    {
        return $this->port;
    }

    /**
     * @return int
     */
    public function getPortApi() : int
    {
        return $this->portApi;
    }

    /**
     * @return Logger
     */
    public function getLogger() : Logger
    {
        return $this->logger;
    }

    /**
     * @inheritdoc
     */
    public function query($query)
    {
        $result = null;
        try {
            $this->logger->startQuery($query);
            $result = $this->connection->query($query);
        } finally {
            $this->logger->stopQuery();
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function multiQuery(array $queue)
    {
        $result = null;
        try {
            $this->logger->startQuery(implode(';', $queue));
            $result = $this->connection->multiQuery($queue);
        } finally {
            $this->logger->stopQuery();
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function escape($value)
    {
        return $this->connection->escape($value);
    }

    /**
     * @inheritdoc
     */
    public function quote($value)
    {
        return $this->connection->quote($value);
    }

    /**
     * @inheritdoc
     */
    public function quoteArr(array $array = [])
    {
        return $this->connection->quoteArr($array);
    }

}
