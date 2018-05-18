<?php

namespace Chebur\SphinxBundle\Profiler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class SphinxDataCollector extends DataCollector
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'chebur.sphinx';
    }

    /**
     * @param Request         $request
     * @param Response        $response
     * @param \Exception|null $exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['queries'] = $this->logger->getQueries();

        $time = 0;
        foreach ($this->data['queries'] as $query) {
            $time += $query['time'];
        }
        $this->data['time'] = $time;
    }

    /**
     * @inheritdoc
     */
    public function reset()
    {
        $this->data = [];
    }

    /**
     * @return array
     */
    public function getQueries() : array
    {
        return $this->data['queries'];
    }

    /**
     * @return float
     */
    public function getTime() : float
    {
        return $this->data['time'];
    }

}
