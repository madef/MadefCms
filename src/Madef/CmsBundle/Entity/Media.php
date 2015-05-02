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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Madef\CmsBundle\Entity\MediaRepository")
 * @ORM\Table(name="media", uniqueConstraints={@ORM\UniqueConstraint(name="media_identifier_version", columns={"identifier", "version_id"})})
 * @ORM\HasLifecycleCallbacks
 */
class Media
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $identifier;

    /**
     * @ORM\ManyToOne(targetEntity="Version")
     * @ORM\JoinColumn(name="version_id", referencedColumnName="id")
     *
     * @var \Madef\CmsBundle\Entity\Version
     */
    private $version;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $mime_type;

    /**
     * @Assert\File(maxSize="6000000")
     *
     * @var string
     */
    private $file;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var Boolean
     */
    private $removed;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $versions;

    public function __construct()
    {
        $this->versions = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set identifier.
     *
     * @param string $identifier
     *
     * @return Layout
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set removed.
     *
     * @param bool $removed
     *
     * @return Layout
     */
    public function setRemoved($removed)
    {
        $this->removed = $removed;

        return $this;
    }

    /**
     * Get removed.
     *
     * @return bool
     */
    public function getRemoved()
    {
        return $this->removed;
    }

    /**
     * Set version.
     *
     * @param \Madef\CmsBundle\Entity\Version $version
     *
     * @return Layout
     */
    public function setVersion(\Madef\CmsBundle\Entity\Version $version = null)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version.
     *
     * @return \Madef\CmsBundle\Entity\Version
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set Versions (non persistant).
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $versions
     *
     * @return \Madef\CmsBundle\Entity\Media
     */
    public function setVersions(ArrayCollection $versions)
    {
        $this->versions = $versions;

        return $this;
    }

    /**
     * Get versions (@see MediaRepository:addVersions).
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Media
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set mimeType.
     *
     * @param string $mimeType
     *
     * @return Media
     */
    public function setMimeType($mimeType)
    {
        $this->mime_type = $mimeType;

        return $this;
    }

    /**
     * Get mimeType.
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mime_type;
    }

    /**
     * Set file.
     *
     * @param string $file
     *
     * @return Media
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get hash.
     *
     * @return string
     */
    public function getHash()
    {
        return md5($this->getIdentifier().'-'.$this->getVersion()->getId());
    }

    public function getUploadDir()
    {
        return __DIR__.'/../../../../app/data/media/';
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null === $this->file) {
            return;
        }

        $this->setMimeType($this->file->getMimeType());
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        $this->file->move($this->getUploadDir(), $this->getHash());
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getUploadDir().$this->getHash()) {
            unlink($file);
        }
    }

    public function isImage()
    {
        list($type, $ext) = explode('/', $this->getMimeType());

        return $type === 'image';
    }
}
