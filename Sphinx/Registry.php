<?php

namespace Chebur\SphinxBundle\Sphinx;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Registry
{
    /**
     * @var Manager[]
     */
    protected $managers;

    /**
     * @var Manager
     */
    protected $managerDefault;

    /**
     * @param ContainerInterface $container
     * @param string[]           $managerNames       Managers with key = name
     * @param string             $managerNameDefault Name of default manager
     */
    public function __construct(ContainerInterface $container, array $managerNames, string $managerNameDefault)
    {
        foreach ($managerNames as $managerName) {
            $manager = $container->get('chebur.sphinx.manager.'.$managerName);
            $this->managers[$managerName] = $manager;
            if ($managerName == $managerNameDefault) {
                $this->managerDefault = $manager;
            }
        }
    }

    /**
     * @return Manager[]
     */
    public function getManagers() : array
    {
        return $this->managers;
    }

    /**
     * @param string|null $name
     * @return Manager|null
     */
    public function getManager(string $name = null) : ?Manager
    {
        if ($name === null) {
            return $this->managerDefault;
        }
        return isset($this->managers[$name]) ? $this->managers[$name] : null;
    }

}
