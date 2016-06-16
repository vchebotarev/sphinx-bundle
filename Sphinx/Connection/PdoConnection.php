<?php

namespace Chebur\SphinxBundle\Sphinx\Connection;

use Foolz\SphinxQL\Drivers\Pdo\Connection as FoolzPdoConnection;

class PdoConnection extends FoolzPdoConnection implements ConnectionInterface
{
    /**
     * @var \PDO
     */
    protected $connection;

    public function ping()
    {
        parent::ping();

        try {
            $this->connection->query('SELECT 1 + 1');
        } catch (\PDOException $e) {
            $this->connect();
        }
    }

}
