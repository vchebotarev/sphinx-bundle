<?php

namespace Chebur\SphinxBundle\Sphinx\Decorator;

use Chebur\SphinxBundle\Profiler\Logger;
use \SphinxClient;

/**
 * @property string $_host
 * @property int    $_port
 * @property int    $_offset
 * @property int    $_limit
 * @property int    $_mode
 * @property array  $_weights
 * @property int    $_sort
 * @property string $_sortby
 * @property int    $_min_id
 * @property int    $_max_id
 * @property array  $_filters
 * @property string $_groupby
 * @property int    $_groupfunc
 * @property string $_groupsort
 * @property string $_groupdistinct
 * @property int    $_maxmatches
 * @property int    $_cutoff
 * @property int    $_retrycount
 * @property int    $_retrydelay
 * @property array  $_anchor
 * @property array  $_indexweights
 * @property int    $_ranker
 * @property string $_rankexpr
 * @property int    $_maxquerytime
 * @property array  $_fieldweights
 * @property array  $_overrides
 * @property string $_select
 * @property int    $_query_flags
 * @property int    $_predictedtime
 * @property string $_outerorderby
 * @property int    $_outeroffset
 * @property int    $_outerlimit
 * @property bool   $_hasouter
 * @property string $_error
 * @property string $_warning
 * @property bool   $_connerror
 * @property array  $_reqs
 * @property string $_mbenc
 * @property bool   $_arrayresult
 * @property int    $_timeout
 *
 * @method GetLastError()
 * @method GetLastWarning()
 * @method IsConnectError()
 * @method SetServer($host, $port = 0)
 * @method SetConnectTimeout($timeout)
 * @method _Send($handle, $data, $length)
 * @method _MBPush()
 * @method _MBPop()
 * @method _Connect()
 * @method _GetResponse($fp, $client_ver)
 * @method SetLimits($offset, $limit, $max=0, $cutoff=0)
 * @method SetMaxQueryTime($max)
 * @method SetMatchMode($mode)
 * @method SetRankingMode($ranker, $rankexpr="")
 * @method SetSortMode($mode, $sortby="")
 * @method SetWeights($weights)
 * @method SetFieldWeights($weights)
 * @method SetIndexWeights($weights)
 * @method SetIDRange($min, $max)
 * @method SetFilter($attribute, $values, $exclude=false)
 * @method SetFilterString($attribute, $value, $exclude=false)
 * @method SetFilterRange($attribute, $min, $max, $exclude=false)
 * @method SetFilterFloatRange($attribute, $min, $max, $exclude=false)
 * @method SetGeoAnchor($attrlat, $attrlong, $lat, $long)
 * @method SetGroupBy($attribute, $func, $groupsort="@group desc")
 * @method SetGroupDistinct($attribute)
 * @method SetRetries($count, $delay=0)
 * @method SetArrayResult($arrayresult)
 * @method SetOverride($attrname, $attrtype, $values)
 * @method SetSelect($select)
 * @method SetQueryFlag($flag_name, $flag_value)
 * @method SetOuterSelect($orderby, $offset, $limit)
 * @method ResetFilters()
 * @method ResetGroupBy()
 * @method ResetOverrides()
 * @method ResetQueryFlag()
 * @method ResetOuterSelect()
 * @method _PackFloat($f)
 * @method AddQuery($query, $index="*", $comment="")
 * @method RunQueries()
 * @method _ParseSearchResponse($response, $nreqs)
 * @method BuildExcerpts($docs, $index, $words, $opts=array())
 * @method BuildKeywords($query, $index, $hits)
 * @method EscapeString($string)
 * @method UpdateAttributes($index, $attrs, $values, $mva=false, $ignorenonexistent=false)
 * @method Open()
 * @method Close()
 * @method Status ($session=false)
 * @method FlushAttributes()
 */
class SphinxClientDecorator
{
    /**
     * @var ConnectionDecorator
     */
    protected $connection;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \SphinxClient
     */
    protected $client;

    /**
     * @param ConnectionDecorator $connection
     */
    public function __construct(ConnectionDecorator $connection)
    {
        $this->connection    = $connection;
        $this->logger        = $connection->getLogger();
        $this->client        = new SphinxClient();
        $this->client->_host = $connection->getHost();
        $this->client->_port = $connection->getPort();
    }

    /**
     * @inheritDoc
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array(array($this->client, $method), $arguments);
    }

    /**
     * @inheritDoc
     */
    public function __get($name)
    {
        return $this->client->$name;
    }

    /**
     * @inheritDoc
     */
    public function __set($name, $value)
    {
        $this->client->$name = $value;
    }

    /**
     * @param string $query
     * @param string $index
     * @param string $comment
     * @return array|bool
     */
    public function query($query, $index = '*', $comment = '')
    {
        $this->logger->startQuery($query . ' (INDEX - ' . $index . ')');
        $result = $this->client->query($query, $index, $comment);
        $this->logger->stopQuery();


        if ($result === false) {
            //$lastError = $this->client->getLastError();
            //todo errors collecting
        }

        return $result;
    }

}
