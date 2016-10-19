<?php

namespace Chebur\SphinxBundle\Sphinx;

use Foolz\SphinxQL\Drivers\ConnectionInterface;
use Foolz\SphinxQL\Helper as FoolzHelper;

class Helper extends FoolzHelper
{
    /**
     * @param ConnectionInterface $connection
     * @return static
     */
    public static function create(ConnectionInterface $connection)
    {
        return new static($connection);
    }

    /**
     * @author https://habrahabr.ru/users/mstarrr/ https://habrahabr.ru/post/132118/
     * @param string $str
     * @return string
     */
    public static function prepareSearchString($str)
    {
        $keyword = array();
        $request = preg_split('/[\s,-]+/', $str, 5);
        $preparedStr       = '';
        if ($request) {
            foreach ($request as $value) {
                if (strlen($value) > 1) {
                    $keyword[] .= '('.$value.' | *'.$value.'* | ='.$value.')'; //emulate expand_keywords
                }
            }
            $preparedStr = implode(' & ', $keyword);
        }
        return $preparedStr;
    }

}
