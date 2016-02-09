<?php

namespace Chebur\SphinxBundle\Sphinx\Decorator;

use Chebur\SphinxBundle\Profiler\Logger;
use Foolz\SphinxQL\Drivers\ConnectionInterface;
use Foolz\SphinxQL\Drivers\Mysqli\Connection as FoolzConnectionMysqli;
use Foolz\SphinxQL\Drivers\Pdo\Connection    as FoolzConnectionPdo;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class ConnectionDecorator implements ConnectionInterface
{
    const DRIVER_PDO    = 'pdo';
    const DRIVER_MYSQLI = 'mysqli';

    const DEFAULT_HOST = 'localhost';
    const DEFAULT_PORT = 9306;

    const DEFAULT_DRIVER = 'pdo';

    /**
     * @var FoolzConnectionMysqli|FoolzConnectionPdo
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
     * @param string $name
     * @param Logger $logger
     * @param string $type
     * @param string $host
     * @param int    $port
     * @todo добавить возможность передавать параметры подключения
     */
    public function __construct($name, Logger $logger, $type = self::DEFAULT_DRIVER, $host = self::DEFAULT_HOST, $port = self::DEFAULT_PORT)
    {
        $this->name = $name;

        $this->logger = $logger;

        switch($type) {
            case self::DRIVER_MYSQLI:
                $driver = new FoolzConnectionMysqli();
                break;
            case self::DRIVER_PDO:
                $driver = new FoolzConnectionPdo();
                break;
            default:
                throw new InvalidArgumentException(
                    'Invalid driver type. "'.$type.'" not in list ('.self::DRIVER_MYSQLI.', '.self::DRIVER_PDO.')'
                );
                break;
        }
        $driver->setParams(array(
            'host' => $host,
            'port' => $port,
        ));
        $this->connection = $driver;

        $this->port = $port;
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * {@inheritdoc }
     */
    public function query($query)
    {
        $result = null;
        try {
            $this->logger->startQuery($query);
            $result = $this->connection->query($query);
        } catch (\Exception $e) {
            //todo фиксировать и ошибки
            throw $e;
        } finally {
            //$meta = $this->connection->query('SHOW META');
            //todo писать и мету запрос (наверно как один не стоит)
            $this->logger->stopQuery();
        }

        return $result;
    }

    /**
     * {@inheritdoc }
     */
    public function multiQuery(Array $queue)
    {
        //todo добавить логирование
        $result = null;
        try {
            $result = $this->connection->multiQuery($queue);
        } catch (\Exception $e) {

            throw $e;
        } finally {

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
    public function quoteIdentifier($value)
    {
        return $this->connection->quoteIdentifier($value);
    }

    /**
     * @inheritdoc
     */
    public function quoteIdentifierArr(array $array = array())
    {
        return $this->connection->quoteIdentifierArr($array);
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
    public function quoteArr(array $array = array())
    {
        return $this->connection->quoteArr($array);
    }

}
