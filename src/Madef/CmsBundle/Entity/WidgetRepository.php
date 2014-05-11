<?php

/*
 * Copyright (c) 2014, de Flotte Maxence <maxence@deflotte.fr>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Madef\CmsBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;

class WidgetRepository extends EntityRepository
{
    /**
     * Get default collection
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDefaultCollection()
    {
        $qb = $this->_em->createQueryBuilder();

        return $qb->select('w')
                ->from('Madef\CmsBundle\Entity\Widget', 'w')
                ->orderBy('w.identifier', 'ASC')
                ->orderBy('w.version', 'DESC')
                ->groupBy('w.identifier')
                ->getQuery()
                ->getResult();
    }

    /**
     * Populate versions
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function addVersions(&$collection)
    {
        foreach ($collection as $item) {
            $versions = new ArrayCollection();
            $qb = $this->_em->createQueryBuilder();
            $items = $qb->select('w')
                    ->from('Madef\CmsBundle\Entity\Widget', 'w')
                    ->orderBy('w.version', 'DESC')
                    ->where('w.identifier = :identifier')
                    ->setParameter('identifier', $item->getIdentifier())
                    ->getQuery()
                    ->getResult();

            foreach ($items as $item) {
                $versions->add($item->getVersion());
            }

            $item->setVersions($versions);
        }
    }

    /**
     * Get widgets collection available for a specify version
     * @param  \Madef\CmsBundle\Entity\Version              $version
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findByVersion($version)
    {
        // Filter by version
        $subqueryVersion = $this->_em->createQueryBuilder();
        $subqueryVersion->select('v')
                ->from('Madef\CmsBundle\Entity\Version', 'v')
                ->where('v.id <= :version_id');

        // Filter the last version of each identifier
        $subqueryWidget = $this->_em->createQueryBuilder();
        $subqueryWidget->select('MAX(w2.version)')
                ->from('Madef\CmsBundle\Entity\Widget', 'w2')
                ->where($subqueryWidget->expr()->in('w2.version', $subqueryVersion->getDQL()))
                ->andWhere('w.identifier = w2.identifier');

        $qb = $this->_em->createQueryBuilder();

        return $qb->select('w')
                ->from('Madef\CmsBundle\Entity\Widget', 'w')
                ->orderBy('w.identifier', 'ASC')
                ->where($qb->expr()->in('w.version', $subqueryWidget->getDQL()))
                ->setParameter('version_id', $version->getId())
                ->getQuery()
                ->getResult();
    }

    /**
     * Get widget using identifier and version
     * @param  string                          $identifier
     * @param  \Madef\CmsBundle\Entity\Version $version
     * @return \Madef\CmsBundle\Entity\Widget
     */
    public function findOneByVersion($identifier, $version)
    {
        $subquery = $this->_em->createQueryBuilder();
        $versions = $subquery->select('v')
                ->from('Madef\CmsBundle\Entity\Version', 'v')
                ->where('v.id <= :version_id');

        $qb = $this->_em->createQueryBuilder();

        return $qb->select('w')
                ->from('Madef\CmsBundle\Entity\Widget', 'w')
                ->orderBy('w.version', 'DESC')
                ->where('w.identifier = :identifier')
                ->andWhere($qb->expr()->in('w.version', $subquery->getDQL()))
                ->setParameter('identifier', $identifier)
                ->setParameter('version_id', $version->getId())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
    }

}
