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
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class FrontController extends Controller
{
    /**
     * @var \Madef\CmsBundle\Entity\Version
     */
    protected $version;
    protected $identifier;
    protected $page;

    /**
     * @param string $hashVersion
     * @param string $path
     *
     * @return type
     *
     * @throws type
     */
    public function indexAction($hashVersion, $identifier)
    {
        $this->identifier = $identifier;
        $this->setVersion($hashVersion);

        return $this->process();
    }

    /**
     * @param string $hashVersion
     *
     * @throws type
     */
    protected function setVersion($hashVersion)
    {
        // Get version using hash
        if ($hashVersion === 'current') {
            $this->version = $this->getDoctrine()->getRepository('MadefCmsBundle:Version')
                    ->findOneByCurrent(true);
        } else {
            $this->version = $this->getDoctrine()->getRepository('MadefCmsBundle:Version')
                    ->findOneByHash($hashVersion);
        }

        if (!$this->version) {
            throw $this->createNotFoundException($this->get('translator')->trans('front.error.entity.page.notfound'));
        }

        return $this->version;
    }

    /**
     * @return type
     *
     * @throws type
     */
    protected function getPage()
    {
        if (empty($this->page)) {
            $this->page = $this->getDoctrine()->getRepository('MadefCmsBundle:Page')
                    ->findOneByVersion($this->identifier, $this->version);

            if (!$this->page) {
                throw $this->createNotFoundException($this->get('translator')->trans('front.error.entity.page.notfound'));
            }
        }

        return $this->page;
    }

    /**
     * @return \Madef\CmsBundle\Entity\Layout
     *
     * @throws \Exception
     */
    protected function getLayout()
    {
        $identifier = $this->getPage()->getLayoutIdentifier();
        $layout = $this->getDoctrine()->getRepository('MadefCmsBundle:Layout')
                ->findOneByVersion($identifier, $this->version);

        if (!$layout) {
            throw new \Exception($this->get('translator')->trans('front.error.entity.page.layout.unknow'));
        }

        return $layout;
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
     * @param object $widgetData
     *
     * @return string
     */
    protected function renderWidget($widgetData)
    {
        $widget = $this->getWidget($widgetData->identifier);

        if (isset($widgetData->vars)) {
            $vars = (array) $widgetData->vars;
        } else {
            $vars = array();
        }
        $rendererClass = $widget->getFrontRenderer();
        $renderer = new $rendererClass($widget, $vars);

        return $renderer->render();
    }

    /**
     * @param object $pageContent
     *
     * @return string
     */
    protected function renderLayout($pageContent)
    {
        $layoutVars = array();
        $layoutVars['_page'] = $this->identifier;
        $layoutVars['_version'] = $this->version;
        foreach ($pageContent as $layoutBlock => $blockContent) {
            $layoutVars[$layoutBlock] = '';
            foreach ($blockContent as $widgetData) {
                $layoutVars[$layoutBlock] .= $this->renderWidget($widgetData);
            }
        }
        $layoutTwigEnvironment = new \Twig_Environment(new \Twig_Loader_String());
        $layoutTwigEnvironment->addFunction(new \Twig_SimpleFunction('url', function ($url, $params) {
                return $this->generateUrl($url, $params);
        }));

        return $layoutTwigEnvironment->render($this->getLayout()->getTemplate(), $layoutVars);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function process()
    {
        $blockContent = $this->getPage()->getContent();
        $content = json_decode($blockContent);
        if (is_null($content)) {
            throw new \Exception($this->get('translator')->trans('front.error.entity.page.content.format'));
        }

        return new Response($this->renderLayout($content));
    }

    /**
     * Return media content.
     *
     * @param string $identifier
     * @param string $version
     * @ParamConverter("version", class="MadefCmsBundle:Version")
     *
     * @return type
     *
     * @throws type
     */
    public function mediaAction($identifier, $version)
    {
        $em = $this->getDoctrine()->getManager();

        $media = $this->getDoctrine()->getRepository('MadefCmsBundle:Media')
                ->findOneBy(array('identifier' => $identifier, 'version' => $version));

        $response = new BinaryFileResponse($media->getUploadDir().$media->getHash());
        $response->headers->set('Content-Type', $media->getMimeType());

        return $response;
    }
}
