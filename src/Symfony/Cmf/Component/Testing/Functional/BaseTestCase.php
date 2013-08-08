<?php

namespace Symfony\Cmf\Component\Testing\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseTestCase extends WebTestCase
{
    protected $db;
    protected $dbManagers = array();

    protected $container;
    protected $containers = array();

    public function getContainer(array $options = array())
    {
        $hash = md5(serialize($options));

        if (!isset($this->containers[$hash])) {
            $client = $this->createClient($options);
            $this->containers[$hash] = $client->getContainer();
        }

        return $this->containers[$hash];
    }

    public function db($type)
    {
        return $this->getDbManager($type);
    }

    public function getDbManager($type)
    {
        if (isset($this->dbManagers[$type])) {
            return $this->dbManagers[$type];
        }

        $className = sprintf(
            'Symfony\Cmf\Component\Testing\Functional\DbManager\%s',
            $type
        );

        if (!class_exists($className)) {
            throw new \InvalidArgumentException(sprintf(
                'Test DBManager "%s" does not exist.',
                $className
            ));
        }

        $dbManager = new $className($this->getContainer());

        $this->dbManagers[$type] = $dbManager;

        return $this->getDbManager($type);
    }
}
