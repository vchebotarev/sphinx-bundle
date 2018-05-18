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
    protected $queries = [];

    /**
     * @var int
     */
    protected $queryCurrent = 0;

    /**
     * @var int
     */
    protected $queryStartTime;

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
    public function getQueries() : array
    {
        return $this->queries;
    }

    /**
     * @todo check previous stopped
     * @param string $sql
     * @return void
     */
    public function startQuery(string $sql) : void
    {
        if ($this->stopwatch) {
            $this->stopwatch->start('sphinx', 'sphinx');
        }

        if ($this->logger) {
            $this->logger->debug($sql);
        }

        $this->queryStartTime = microtime(true);
        $this->queries[$this->queryCurrent] = [
            'sql'  => $sql,
            'time' => 0,
        ];

    }

    /**
     * @todo check has started
     * @return void
     */
    public function stopQuery() : void
    {
        if ($this->stopwatch) {
            $this->stopwatch->stop('sphinx');
        }
        $time = round(microtime(true) - $this->queryStartTime, 5) * 1000;
        $this->queries[$this->queryCurrent]['time'] = $time;
        $this->queryCurrent++;
    }

}
