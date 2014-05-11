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

class LayoutRepository extends EntityRepository
{
    /**
     * Get the list of identifiers
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getIdentifierList()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('l.identifier')
                ->from('Madef\CmsBundle\Entity\Layout', 'l')
                ->orderBy('l.identifier', 'ASC')
                ->groupBy('l.identifier');

        $results = new ArrayCollection();
        foreach ($qb->getQuery()->getResult() as $row) {
            $results->set($row['identifier'], $row['identifier']);
        }

        return $results;
    }

    /**
     * Get default collection
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDefaultCollection()
    {
        $qb = $this->_em->createQueryBuilder();

        return $qb->select('l')
                ->from('Madef\CmsBundle\Entity\Layout', 'l')
                ->orderBy('l.identifier', 'ASC')
                ->orderBy('l.version', 'DESC')
                ->groupBy('l.identifier')
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
            $items = $qb->select('l')
                    ->from('Madef\CmsBundle\Entity\Layout', 'l')
                    ->orderBy('l.version', 'DESC')
                    ->where('l.identifier = :identifier')
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
     * Get layouts collection available for a specify version
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
        $subqueryLayout = $this->_em->createQueryBuilder();
        $subqueryLayout->select('MAX(l2.version)')
                ->from('Madef\CmsBundle\Entity\Layout', 'l2')
                ->where($subqueryLayout->expr()->in('l2.version', $subqueryVersion->getDQL()))
                ->andWhere('l.identifier = l2.identifier');

        $qb = $this->_em->createQueryBuilder();

        return $qb->select('l')
                ->from('Madef\CmsBundle\Entity\Layout', 'l')
                ->orderBy('l.identifier', 'ASC')
                ->where($qb->expr()->in('l.version', $subqueryLayout->getDQL()))
                ->setParameter('version_id', $version->getId())
                ->getQuery()
                ->getResult();
    }

    /**
     * Get layout using identifier and version
     * @param  string                          $identifier
     * @param  \Madef\CmsBundle\Entity\Version $version
     * @return \Madef\CmsBundle\Entity\Layout
     */
    public function findOneByVersion($identifier, $version)
    {
        $subquery = $this->_em->createQueryBuilder();
        $versions = $subquery->select('v')
                ->from('Madef\CmsBundle\Entity\Version', 'v')
                ->where('v.id <= :version_id');

        $qb = $this->_em->createQueryBuilder();

        return $qb->select('l')
                ->from('Madef\CmsBundle\Entity\Layout', 'l')
                ->orderBy('l.version', 'DESC')
                ->where('l.identifier = :identifier')
                ->andWhere($qb->expr()->in('l.version', $subquery->getDQL()))
                ->setParameter('identifier', $identifier)
                ->setParameter('version_id', $version->getId())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
    }

    /**
     * Get number of layout
     * @return int
     */
    public function count()
    {
        return $this->createQueryBuilder('id')
            ->select('COUNT(id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

}
