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
     * @var Helper|null
     */
    protected $helper;

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
    public function createSphinxApi() : SphinxClientDecorator
    {
        return new SphinxClientDecorator($this->connection);
    }

    /**
     * @return SphinxQL
     */
    public function createQueryBuilder() : SphinxQL
    {
        return new SphinxQL($this->connection);
    }

    /**
     * @return Helper
     */
    public function getHelper() : Helper
    {
        if ($this->helper === null) {
            $this->helper = new Helper($this);
        }
        return $this->helper;
    }

    /**
     * @return ConnectionDecorator
     */
    public function getConnection() : ConnectionDecorator
    {
        return $this->connection;
    }

}
