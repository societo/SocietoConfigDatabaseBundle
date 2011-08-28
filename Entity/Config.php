<?php

/**
 * SocietoConfigDatabaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\Config\DatabaseBundle\Entity;

use Societo\BaseBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="config")
 */
class Config extends BaseEntity
{
    /**
     * @ORM\Column(name="name", type="text")
     */
    protected $name;

    /**
     * @ORM\Column(name="value", type="array")
     */
    protected $value;

    /**
     * @ORM\Column(name="type", type="text")
     */
    protected $type = '';

    public function __construct($name, $value = null, $type = '')
    {
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getType()
    {
        return $this->type;
    }
}
