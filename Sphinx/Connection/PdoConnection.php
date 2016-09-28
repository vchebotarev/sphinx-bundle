<?php

namespace Chebur\SphinxBundle\Sphinx\Connection;

use Foolz\SphinxQL\Drivers\Pdo\Connection as FoolzPdoConnection;

class PdoConnection extends FoolzPdoConnection implements ConnectionInterface
{
    /**
     * @var \PDO
     */
    protected $connection;

    /**
     * Потому что silenceConnectionWarning deprecated
     * потому что Exception лучше error (тем более делать trigger_error руками - провал)
     * @var bool
     */
    protected $silence_connection_warning = true;

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
