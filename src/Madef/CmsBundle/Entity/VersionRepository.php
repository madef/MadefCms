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

class VersionRepository extends EntityRepository
{

    /**
     * Get the list of version not published
     * @return array
     */
    public function getNotPublishedQuery()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('v')
                ->from('Madef\CmsBundle\Entity\Version', 'v')
                ->orderBy('v.identifier', 'ASC')
                ->where('v.published_at IS NULL');

        return $qb;
    }

    /**
     * Get the list of version published
     * @return array
     */
    public function getPublishedQuery()
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('v')
                ->from('Madef\CmsBundle\Entity\Version', 'v')
                ->orderBy('v.identifier', 'ASC')
                ->where('v.published_at IS NOT NULL');

        return $qb;
    }

    /**
     * Get default collection
     * @return array
     */
    public function getDefaultCollection()
    {
        $qb = $this->_em->createQueryBuilder();

        return $qb->select('v')
                ->from('Madef\CmsBundle\Entity\Version', 'v')
                ->orderBy('v.created_at', 'ASC')
                ->getQuery()
                ->getResult();
    }

    /**
     * Get the current version
     * @return \Madef\CmsBundle\Entity\Version
     */
    public function getCurrentVersion()
    {
        $qb = $this->_em->createQueryBuilder();

        return $qb->select('v')
                ->from('Madef\CmsBundle\Entity\Version', 'v')
                ->where('v.current = 1')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
    }
}
