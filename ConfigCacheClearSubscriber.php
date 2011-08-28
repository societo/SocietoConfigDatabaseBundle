<?php

/**
 * SocietoConfigDatabaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\Config\DatabaseBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Societo\Config\DatabaseBundle\Entity\Config;
use Doctrine\ORM\Events;

class ConfigCacheClearSubscriber implements \Doctrine\Common\EventSubscriber
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function onFlush($e)
    {
        $em = $e->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() AS $entity) {
            if ($this->cacheClearForEntity($em, $entity)) {
                return;
            }
        }

        foreach ($uow->getScheduledEntityUpdates() AS $entity) {
            if ($this->cacheClearForEntity($em, $entity)) {
                return;
            }
        }
    }

    private function cacheClearForEntity($em, $entity)
    {
        if ($entity instanceof Config) {
            $cacheDir = $this->container->getParameter('kernel.cache_dir');
            $oldCacheDir  = $cacheDir.'_old';
            $filesystem = $this->container->get('filesystem');

            try {
                $filesystem->rename($cacheDir, $oldCacheDir);
                $filesystem->remove($cacheDir);
                $filesystem->remove($oldCacheDir);

                $filesystem->mkdir($cacheDir.'/annotations');
            } catch (\Exception $e) {
            }

            return true;
        }
    }

    public function getSubscribedEvents()
    {
        return array(Events::onFlush);
    }
}
