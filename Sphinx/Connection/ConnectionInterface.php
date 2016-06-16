<?php

namespace Chebur\SphinxBundle\Sphinx\Connection;

use Foolz\SphinxQL\Drivers\ConnectionInterface as FoolzConnectionInterface;

interface ConnectionInterface extends FoolzConnectionInterface
{
    public function ping();

}
