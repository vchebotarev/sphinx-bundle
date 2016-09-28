<?php

namespace Chebur\SphinxBundle\Sphinx\Connection;

use Foolz\SphinxQL\Drivers\Mysqli\Connection as FoolzMysqliConnection;

class MysqliConnection extends FoolzMysqliConnection implements ConnectionInterface
{
    /**
     * потому что Exception лучше error
     * @var bool
     */
    protected $silence_connection_warning = true;

}
