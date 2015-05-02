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

namespace Madef\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

abstract class AbstractFrontAssetController extends Controller
{
    const TYPE_CSS = 'css';
    const TYPE_JS = 'js';

    protected $version;
    protected $pageIdentifier;
    protected $type;

    /**
     * @return type
     *
     * @throws type
     */
    protected function getWidgets()
    {
        $page = $this->getDoctrine()->getRepository('MadefCmsBundle:Page')
                ->findOneByVersion($this->pageIdentifier, $this->version);

        if (!$page) {
            throw $this->createNotFoundException($this->get('translator')->trans('front.error.entity.page.notfound'));
        }

        $widgets = array();
        $pageContent = json_decode($page->getContent(), true);
        foreach ($pageContent as $areaContent) {
            foreach ($areaContent as $widgetData) {
                $widgets[] = $this->getWidget($widgetData['identifier']);
            }
        }

        return $widgets;
    }

    /**
     * @param string $identifier
     *
     * @return \Madef\CmsBundle\Entity\Widget
     *
     * @throws \Exception
     */
    protected function getWidget($identifier)
    {
        $widget = $this->getDoctrine()->getRepository('MadefCmsBundle:Widget')
                ->findOneByVersion($identifier, $this->version);

        if (!$widget) {
            throw new \Exception($this->get('translator')->trans('front.error.entity.page.widget.unknow'));
        }

        return $widget;
    }

    /**
     * Get file cache path.
     *
     * @return string
     */
    protected function getFileCachePath()
    {
        return $this->container->getParameter('kernel.cache_dir').'/madefcms/'.md5($this->pageIdentifier.$this->version->getIdentifier()).'.'.$this->type;
    }

    /**
     * Set cache content.
     */
    protected function setCache($data)
    {
        $filename = $this->getFileCachePath();
        $fs = new Filesystem();
        try {
            $fs->mkdir(dirname($filename));
        } catch (IOException $e) {
            echo 'An error occured while creating cache directory';
        }

        return file_put_contents($filename, $data);
    }

    /**
     * Get cache content.
     *
     * @return string
     */
    protected function getCache()
    {
        return file_get_contents($this->getFileCachePath());
    }

    /**
     * Cache exists?
     *
     * @return bool
     */
    protected function cacheExists()
    {
        return file_exists($this->getFileCachePath());
    }

    /**
     * Generate asset content.
     */
    protected function generate()
    {
        $collection = array();
        $widgets = $this->getWidgets();
        foreach ($widgets as $widget) {
            $collection[] = $this->getWidgetAsset($widget);
        }

        $data = implode("\n", $collection);
        $this->setCache($data);
    }

    /**
     * Return asset content.
     *
     * @param string $identifier
     * @param string $version
     * @ParamConverter("version", class="MadefCmsBundle:Version")
     *
     * @return type
     *
     * @throws type
     */
    public function getAction($page, $version)
    {
        $this->pageIdentifier = $page;
        $this->version = $version;

        if (!$version->wasPublished() || !$this->cacheExists()) {
            $this->generate();
        }

        $response = new Response($this->getCache());
        $response->headers->set('Content-Type', 'text/css');

        return $response;
    }
}
