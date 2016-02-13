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
        if ($this->type != 'select') {
            return $this->select($select);
        }
        $selectSet = is_array($select) ? $select : func_get_args();
        foreach($selectSet as $selectItem) {
            $this->select[] = $selectItem;
        }
        return $this;
    }

}
