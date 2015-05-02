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
use Symfony\Component\HttpFoundation\Request;
use Madef\CmsBundle\Entity\Media;
use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AdminMediaController extends Controller
{
    /**
     * @return type
     */
    public function listAction()
    {
        $collection = $this->getDoctrine()->getRepository('MadefCmsBundle:Media')
                ->getDefaultCollection();

        $this->getDoctrine()->getRepository('MadefCmsBundle:Media')
                ->addVersions($collection);

        return $this->render('MadefCmsBundle:AdminMedia:list.html.twig', array(
            'medias' => $collection,
        ));
    }

    /**
     * Display form to create a Media.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return type
     */
    public function addAction(Request $request)
    {
        $media = new Media();
        $media->setRemoved(false);

        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder($media)
            ->add('identifier', 'text', array(
                'attr' => array(
                    'placeholder' => 'media-identifier',
            ), ))
            ->add('name', 'text')
            ->add('file')
            ->add('version', 'entity', array(
                'class' => 'MadefCmsBundle:Version',
                'empty_value'  => '',
                'property' => 'identifier',
                'query_builder' => function (EntityRepository $er) {
                    return $er->getNotPublishedQuery();
                },
            ))
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($media);
            $em->flush();

            return $this->redirect($this->generateUrl('madef_cms_admin_media_list'));
        }

        return $this->render('MadefCmsBundle:Admin:form.html.twig', array(
            'form' => $form->createView(),
            'title' => $this->get('translator')->trans('admin.media.page.add.title'),
        ));
    }

    /**
     * Display form to edit a Media.
     *
     * @ParamConverter("version", class="MadefCmsBundle:Version")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string                                    $identifier
     * @param string                                    $version
     *
     * @return type
     */
    public function editAction(Request $request, $identifier, $version)
    {
        $em = $this->getDoctrine()->getManager();

        $currentMedia = $this->getDoctrine()->getRepository('MadefCmsBundle:Media')
                ->findOneBy(array('identifier' => $identifier, 'version' => $version));

        if (!$currentMedia) {
            throw $this->createNotFoundException('error.entity.media.notfound');
        }

        // Use new media if version is published
        if ($version->wasPublished()) {
            $media = new Media();
            $media->setIdentifier($currentMedia->getIdentifier());
            $media->setStructure($currentMedia->getStructure());
            $media->setTemplate($currentMedia->getTemplate());
            $media->setDefaultContent($currentMedia->getDefaultContent());
            $media->setRemoved(false);
        } else {
            $media = $currentMedia;
        }

        $form = $this->createFormBuilder($media)
            ->add('file')
            ->add('version', 'entity', array(
                'class' => 'MadefCmsBundle:Version',
                'empty_value'  => '',
                'property' => 'identifier',
                'query_builder' => function (EntityRepository $er) {
                    return $er->getNotPublishedQuery();
                },
            ))
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($media);
            $em->flush();

            return $this->redirect($this->generateUrl('madef_cms_admin_media_list'));
        }

        return $this->render('MadefCmsBundle:AdminMedia:form.html.twig', array(
            'form' => $form->createView(),
            'media' => $media,
            'title' => $this->get('translator')->trans('admin.media.page.edit.title'),
        ));
    }
}
