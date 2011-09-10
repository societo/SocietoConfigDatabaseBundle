<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\Config\DatabaseBundle\Tests\Config\Resource;

use Societo\Config\DatabaseBundle\Config\Resource\DatabaseResource;

class DatabaseResourceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetResourceAndToString()
    {
        $dbResource = new DatabaseResource($this->getEntityManagerMock(), 'db:example');

        $this->assertEquals('db:example', $dbResource->getResource());
        $this->assertEquals('db:example', (string)$dbResource);
    }

    public function testGetResourceName()
    {
        $dbResource = new DatabaseResource($this->getEntityManagerMock(), 'db:example');

        $this->assertEquals('example', $dbResource->getResourceName());
    }

    public function getEntityManagerMock()
    {
        $conn = $this->getMock('Doctrine\DBAL\Connection', array(), array(), '', false);

        $em = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);
        $em->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($conn));

        return $em;
    }
}
