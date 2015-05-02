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

class AdminHomeController extends Controller
{
    /**
     * @return type
     */
    public function indexAction()
    {
        $hasVersion = (bool) $this->getDoctrine()->getRepository('MadefCmsBundle:Version')
                ->count();
        $hasLayout = (bool) $this->getDoctrine()->getRepository('MadefCmsBundle:Layout')
                ->count();
        $hasWidget = (bool) $this->getDoctrine()->getRepository('MadefCmsBundle:Widget')
                ->count();
        $hasPage = (bool) $this->getDoctrine()->getRepository('MadefCmsBundle:Page')
                ->count();
        $hasBeenPublished = (bool) $this->getDoctrine()->getRepository('MadefCmsBundle:Version')
                ->countPublished();

        if (!$hasVersion) {
            $step = 'version';
        } elseif (!$hasLayout) {
            $step = 'layout';
        } elseif (!$hasWidget) {
            $step = 'widget';
        } elseif (!$hasPage) {
            $step = 'page';
        } elseif (!$hasBeenPublished) {
            $step = 'publish';
        } else {
            $step = 'final';
            $securityContext = $this->container->get('security.context');
            if ($securityContext->isGranted('ROLE_PAGE_VIEW')) {
                return $this->redirect($this->generateUrl('madef_cms_admin_page_list'));
            } elseif ($securityContext->isGranted('ROLE_LAYOUT')) {
                return $this->redirect($this->generateUrl('madef_cms_admin_layout_list'));
            } elseif ($securityContext->isGranted('ROLE_WIDGET')) {
                return $this->redirect($this->generateUrl('madef_cms_admin_widget_list'));
            } elseif ($securityContext->isGranted('ROLE_MEDIA')) {
                return $this->redirect($this->generateUrl('madef_cms_admin_media_list'));
            } elseif ($securityContext->isGranted('ROLE_VERSION_VIEW')) {
                return $this->redirect($this->generateUrl('madef_cms_admin_version_list'));
            } else {
                throw $this->createNotFoundException('Not enought right');
            }
        }

        return $this->render('MadefCmsBundle:AdminHome:step-'.$step.'.html.twig');
    }
}
