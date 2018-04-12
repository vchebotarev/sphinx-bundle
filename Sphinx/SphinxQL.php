<?php

namespace Chebur\SphinxBundle\Sphinx;

use Foolz\SphinxQL\SphinxQL as SphinxQLBase;

class SphinxQL extends SphinxQLBase
{
    /**
     * @param array|string $select
     * @return $this
     */
    public function addSelect($select)
    {
        $select = is_array($select) ? $select : func_get_args();
        $this->select = array_merge($this->select, $select);
        return $this;
    }

}
