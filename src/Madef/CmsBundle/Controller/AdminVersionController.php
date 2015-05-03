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

use Symfony\Component\HttpFoundation\Request;
use Madef\CmsBundle\Entity\Version;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AdminVersionController extends AbstractAdminController
{
    /**
     * @return type
     */
    public function listAction()
    {
        $collection = $this->getDoctrine()->getRepository('MadefCmsBundle:Version')
                ->getDefaultCollection();

        return $this->render('MadefCmsBundle:AdminVersion:list.html.twig', array(
            'versions' => $collection,
        ));
    }

    /**
     * Display form to create a Version.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return type
     */
    public function addAction(Request $request)
    {
        $version = new Version();
        $version->setCurrent(false);

        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder($version)
            ->add('identifier', 'text', array(
                'attr' => array(
                    'placeholder' => '1 - initial version',
            ), ))
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $version->setHash(md5($version->getIdentifier().rand(1, getrandmax())));
            $version->setCreatedAt(new \DateTime());
            $em->persist($version);
            $em->flush();

            return $this->redirect($this->generateUrl('madef_cms_admin_version_list'));
        }

        return $this->render('MadefCmsBundle:Admin:form.html.twig', array(
            'form' => $form->createView(),
            'title' => $this->get('translator')->trans('admin.widget.page.add.title'),
        ));
    }
    /**
     * Publish a Version.
     *
     * @ParamConverter("version", class="MadefCmsBundle:Version")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Madef\CmsBundle\Entity\Version           $version
     *
     * @return type
     */
    public function publishAction(Request $request, \Madef\CmsBundle\Entity\Version $version)
    {
        $em = $this->getDoctrine()->getManager();

        $currentVersion = $this->getDoctrine()->getRepository('MadefCmsBundle:Version')
                ->getCurrentVersion();
        if ($currentVersion) {
            $currentVersion->setCurrent(false);
            $em->persist($currentVersion);
        }

        $version->setCurrent(true);
        $version->setPublishedAt(new \DateTime());
        $em->persist($version);

        $em->flush();

        return $this->redirect($this->generateUrl('madef_cms_admin_version_list'));
    }
}
