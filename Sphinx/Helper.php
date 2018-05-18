<?php

namespace Chebur\SphinxBundle\Sphinx;

use Foolz\SphinxQL\Drivers\ConnectionInterface;
use Foolz\SphinxQL\Helper as FoolzHelper;

class Helper extends FoolzHelper
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        parent::__construct($manager->getConnection());
    }

    /**
     * @inheritDoc
     */
    public static function create(ConnectionInterface $connection)
    {
        throw new \Exception('Use "new Helper($manager)" syntax if you need new object');
    }

    /**
     * @inheritdoc
     */
    protected function getSphinxQL()
    {
        return $this->manager->createQueryBuilder();
    }

    /**
     * @author https://habrahabr.ru/users/mstarrr/ https://habrahabr.ru/post/132118/
     * @param string $str
     * @return string
     */
    public static function prepareSearchString(string $str) : string
    {
        $keyword = [];
        $request = preg_split('/[\s,-]+/', $str, 5);
        $preparedStr = '';
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
