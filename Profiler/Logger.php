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
     * @param string $sql
     * @return void
     */
    public function startQuery($sql)
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('sphinx', 'sphinx');
        }

        if ($this->logger) {
            $this->logger->debug($sql);
        }

        $this->queryStart = microtime(true);
        $this->queries[$this->queryCurrent] = array(
            'sql'  => $sql,
            'time' => 0,
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
        $time = round(microtime(true) - $this->queryStart, 5) * 1000;
        $this->queries[$this->queryCurrent]['time'] = $time;
        $this->queryCurrent++;
    }

}
