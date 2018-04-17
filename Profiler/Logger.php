<?php

namespace Chebur\SphinxBundle\Profiler;

use Psr\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class Logger
{
    /**
     * @var Stopwatch
     */
    protected $stopwatch;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $queries = array();

    /**
     * @var array
     */
    protected $errors  = array();

    /**
     * @var int
     */
    protected $queryCurrent = 0;

    /**
     * @var int
     */
    protected $queryStart;

    /**
     * @param LoggerInterface|null $logger
     * @param Stopwatch|null       $stopwatch
     */
    public function __construct(LoggerInterface $logger = null, Stopwatch $stopwatch = null)
    {
        $this->logger    = $logger;
        $this->stopwatch = $stopwatch;
    }

    /**
     * @return array
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param string     $sql    The SQL to be executed.
     * @param array|null $params The SQL parameters.
     * @param array|null $types  The SQL parameter types.
     * @return void
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('sphinx', 'sphinx');
        }

        if ($this->logger) {
            $this->logger->debug($sql, $params === null ? array() : $params);
        }

        $this->queryStart = microtime(true);
        $this->queries[$this->queryCurrent] = array(
            'sql'     => $sql,
            'params'  => $params,
            'time'    => 0,
            //'realSql' => $realSql,
        );

    }

    /**
     * @return void
     */
    public function stopQuery()
    {
        if ($this->stopwatch) {
            $this->stopwatch->stop('sphinx');
        }

        $this->queries[$this->queryCurrent]['time'] = microtime(true) - $this->queryStart;
        $this->queryCurrent++;
    }

    public function logError()
    {
        //todo
    }

}
