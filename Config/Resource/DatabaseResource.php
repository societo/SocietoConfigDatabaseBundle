<?php

/**
 * SocietoConfigDatabaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\Config\DatabaseBundle\Config\Resource;

use Symfony\Component\Config\Resource\ResourceInterface;

class DatabaseResource implements ResourceInterface
{
    protected $resource, $params, $conn;

    public function __construct($em, $resource)
    {
        $this->resource = $resource;
        $this->params = $em->getConnection()->getParams();
    }

    public function __toString()
    {
        return (string)$this->resource;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function getConnection()
    {
        if (!$this->conn) {
            $this->conn = \Doctrine\DBAL\DriverManager::getConnection($this->params);
            $this->conn->connect();
        }

        return $this->conn;
    }

    public function getResourceName()
    {
        return substr($this->resource, strlen('db:'));
    }

    public function getValue()
    {
        try {
            $value = $this->getConnection()->fetchColumn('SELECT value FROM Config WHERE name = ?', array($this->getResourceName()));
        } catch (\Exception $e) {
            return null;
        }

        return unserialize($value);
    }

    public function getUpdatedAt()
    {
        return $this->getConnection()->fetchColumn('SELECT updated_at FROM Config WHERE name = ?', array($this->getResourceName()));
    }

    public function isFresh($timestamp)
    {
        try {
            return $timestamp > strtotime($this->getUpdatedAt());
        } catch (\Exception $e) {
            return true;
        }
    }

    public function __sleep()
    {
        return array('resource', 'params');
    }
}
