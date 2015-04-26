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
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Madef\CmsBundle\Entity\WidgetRepository")
 * @ORM\Table(name="widget", uniqueConstraints={@ORM\UniqueConstraint(name="widget_identifier_version", columns={"identifier", "version_id"})})
 */
class Widget
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $identifier;

    /**
     * @ORM\ManyToOne(targetEntity="Version")
     * @ORM\JoinColumn(name="version_id", referencedColumnName="id")
     * @var \Madef\CmsBundle\Entity\Version
     */
    private $version;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $form;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $template;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $default_content;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $front_renderer;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $back_renderer;

    /**
     * @ORM\Column(type="boolean")
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set identifier
     *
     * @param  string $identifier
     * @return Widget
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set form
     *
     * @param  string $form
     * @return Widget
     */
    public function setForm($form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get form
     *
     * @return string
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set template
     *
     * @param  string $template
     * @return Widget
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set defaultContent
     *
     * @param  string $default_content
     * @return Widget
     */
    public function setDefaultContent($default_content)
    {
        $this->default_content = $default_content;

        return $this;
    }

    /**
     * Get defaultContent
     *
     * @return string
     */
    public function getDefaultContent()
    {
        return $this->default_content;
    }

    /**
     * Set removed
     *
     * @param  boolean $removed
     * @return Widget
     */
    public function setRemoved($removed)
    {
        $this->removed = $removed;

        return $this;
    }

    /**
     * Get removed
     *
     * @return boolean
     */
    public function getRemoved()
    {
        return $this->removed;
    }

    /**
     * Set version
     *
     * @param  \Madef\CmsBundle\Entity\Version $version
     * @return Widget
     */
    public function setVersion(\Madef\CmsBundle\Entity\Version $version = null)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return \Madef\CmsBundle\Entity\Version
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set Versions (non persistant)
     * @param  \Doctrine\Common\Collections\ArrayCollection $versions
     * @return \Madef\CmsBundle\Entity\Page
     */
    public function setVersions(ArrayCollection $versions)
    {
        $this->versions = $versions;

        return $this;
    }

    /**
     * Get versions (@see PageRepository:addVersions)
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * Set front renderer class
     *
     * @param string $frontRenderer
     *
     * @return Widget
     */
    public function setFrontRenderer($frontRenderer)
    {
        $this->front_renderer = $frontRenderer;

        return $this;
    }

    /**
     * Get front renderer class
     *
     * @return string
     */
    public function getFrontRenderer()
    {
        return $this->front_renderer;
    }

    /**
     * Set back renderer class
     *
     * @param string $backRenderer
     *
     * @return Widget
     */
    public function setBackRenderer($backRenderer)
    {
        $this->back_renderer = $backRenderer;

        return $this;
    }

    /**
     * Get back renderer class
     *
     * @return string
     */
    public function getBackRenderer()
    {
        return $this->back_renderer;
    }
}
