<?php

namespace Chebur\SphinxBundle\Sphinx;

use Chebur\SphinxBundle\Sphinx\Decorator\ConnectionDecorator;
use Chebur\SphinxBundle\Sphinx\Decorator\SphinxClientDecorator;

class Manager
{
    const DEFAULT_NAME = 'default';

    /**
     * @var ConnectionDecorator
     */
    protected $connection;

    /**
     * @param ConnectionDecorator $connection
     */
    public function __construct(ConnectionDecorator $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return SphinxClientDecorator
     */
    public function createSphinxApi()
    {
        return new SphinxClientDecorator($this->connection);
    }

    /**
     * @return SphinxQL
     */
    public function createQueryBuilder()
    {
        return new SphinxQL($this->connection);
    }

    /**
     * @return ConnectionDecorator
     */
    public function getConnection()
    {
        return $this->connection;
    }

}
