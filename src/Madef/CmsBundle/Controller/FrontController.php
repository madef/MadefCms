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

class FrontController extends Controller
{
    /**
     *
     * @var \Madef\CmsBundle\Entity\Version
     */
    protected $version;

    /**
     *
     * @param  string $hashVersion
     * @param  string $path
     * @return type
     * @throws type
     */
    public function indexAction($hashVersion, $identifier)
    {
        $version = $this->getVersion($hashVersion);
        $page = $this->getPage($identifier, $version);
        $layout = $this->getLayout($page->getLayoutIdentifier(), $version);

        return $this->process($layout->getTemplate(), $page->getContent(), $version);
    }

    /**
     *
     * @param  string                          $hashVersion
     * @return \Madef\CmsBundle\Entity\Version
     * @throws type
     */
    protected function getVersion($hashVersion)
    {
        // Get version using hash
        if ($hashVersion === 'current') {
            $version = $this->getDoctrine()->getRepository('MadefCmsBundle:Version')
                    ->findOneByCurrent(true);
        } else {
            $version = $this->getDoctrine()->getRepository('MadefCmsBundle:Version')
                    ->findOneByHash($hashVersion);
        }

        if (!$version) {
            throw $this->createNotFoundException($this->get('translator')->trans('front.error.entity.page.notfound'));
        }

        return $version;
    }

    /**
     *
     * @param  string                          $identifier
     * @param  \Madef\CmsBundle\Entity\Version $version
     * @return type
     * @throws type
     */
    protected function getPage($identifier, \Madef\CmsBundle\Entity\Version $version)
    {
        $page = $this->getDoctrine()->getRepository('MadefCmsBundle:Page')
                ->findOneByVersion($identifier, $version);

        if (!$page) {
            throw $this->createNotFoundException($this->get('translator')->trans('front.error.entity.page.notfound'));
        }

        return $page;
    }

    /**
     *
     * @param  string                          $identifier
     * @param  \Madef\CmsBundle\Entity\Version $version
     * @return \Madef\CmsBundle\Entity\Layout
     * @throws \Exception
     */
    protected function getLayout($identifier, \Madef\CmsBundle\Entity\Version $version)
    {
        $layout = $this->getDoctrine()->getRepository('MadefCmsBundle:Layout')
                ->findOneByVersion($identifier, $version);

        if (!$layout) {
            throw new \Exception($this->get('translator')->trans('front.error.entity.page.layout.unknow'));
        }

        return $layout;
    }

    /**
     *
     * @param  string                          $identifier
     * @param  \Madef\CmsBundle\Entity\Version $version
     * @return \Madef\CmsBundle\Entity\Layout
     * @throws \Exception
     */
    protected function getWidget($identifier, \Madef\CmsBundle\Entity\Version $version)
    {
        $widget = $this->getDoctrine()->getRepository('MadefCmsBundle:Widget')
                ->findOneByVersion($identifier, $version);

        if (!$widget) {
            throw new \Exception($this->get('translator')->trans('front.error.entity.page.widget.unknow'));
        }

        return $widget;
    }

    /**
     *
     * @param  object                          $widgetData
     * @param  \Madef\CmsBundle\Entity\Version $version
     * @return string
     */
    public function renderWidget($widgetData, \Madef\CmsBundle\Entity\Version $version)
    {
        $widget = $this->getWidget($widgetData->identifier, $version);
        $widgetTwigEnvironment = new \Twig_Environment(new \Twig_Loader_String());
        if (isset($widgetData->vars)) {
            $vars = (array) $widgetData->vars;
        } else {
            $vars = array();
        }

        return $widgetTwigEnvironment->render($widget->getTemplate(), $vars);
    }

    /**
     *
     * @param  object                          $pageContent
     * @param  string                          $layoutTemplate
     * @param  \Madef\CmsBundle\Entity\Version $version
     * @return string
     */
    protected function renderLayout($pageContent, $layoutTemplate, \Madef\CmsBundle\Entity\Version $version)
    {
        $layoutVars = array();
        foreach ($pageContent as $layoutBlock => $blockContent) {
            $layoutVars[$layoutBlock] = '';
            foreach ($blockContent as $widgetData) {
                $layoutVars[$layoutBlock] .= $this->renderWidget($widgetData, $version);
            }
        }
        $layoutTwigEnvironment = new \Twig_Environment(new \Twig_Loader_String());

        return $layoutTwigEnvironment->render($layoutTemplate, $layoutVars);
    }

    /**
     *
     * @param  string                                     $layoutTemplate
     * @param  string                                     $blockContent
     * @param  \Madef\CmsBundle\Entity\Version            $version
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function process($layoutTemplate, $blockContent, \Madef\CmsBundle\Entity\Version $version)
    {
        $content = json_decode($blockContent);
        if (is_null($content)) {
            throw new \Exception($this->get('translator')->trans('front.error.entity.page.content.format'));
        }

        return new Response($this->renderLayout($content, $layoutTemplate, $version));
    }
}
