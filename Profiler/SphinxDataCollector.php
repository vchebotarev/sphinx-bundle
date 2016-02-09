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

        $this->data['errors']  = $this->logger->getErrors();

        $time = 0;
        foreach($this->data['queries'] as $query){
            $time += $query['time'];
        }
        $this->data['time'] = $time; //todo перепроверить
    }

    /**
     * @return array
     */
    public function getQueries()
    {
        return $this->data['queries'];
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->data['errors'];
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->data['time'];
    }

}
