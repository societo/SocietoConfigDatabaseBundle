<?php

/**
 * SocietoConfigDatabaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\Config\DatabaseBundle\Routing\Loader;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Societo\Config\DatabaseBundle\Config\Resource\DatabaseResource;

class DatabaseLoader extends Loader
{
    private $em;

    /**
     * Constructor.
     */
    public function __construct($em)
    {
        $this->em = $em;
    }

    public function load($resource, $type = null)
    {
        $dbResource = new DatabaseResource($this->em, $resource);

        $collection = new RouteCollection();
        $collection->addResource($dbResource);

        $list = array();
        try {
            $list = $dbResource->getValue();
        } catch (\PDOException $e) {
            // FIXME: we cannot avoid this exception in Societo installation
            // FIXME: MySQL specific
            $allow = array(
                '1049', // error code 1049 means "unknown database" in MySQL
                '42S02',
            );

            if (!in_array($e->getCode(), $allow)) {
                throw $e;
            }
        }

        if (!$list) {
            return $collection;
        }

        foreach ($list as $name => $item) {
            $route = new Route($item['pattern'], $item['defaults'], $item['requirements'], $item['options']);
            $collection->add($name, $route);
        }

        return $collection;
    }

    /**
     * Returns true if this class supports the given resource.
     *
     * @param mixed  $resource A resource
     * @param string $type     The resource type
     *
     * @return Boolean true if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return (0 === strpos($resource, 'db:'));
    }
}
