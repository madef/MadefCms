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
use Madef\CmsBundle\Entity\Page;
use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AdminPageController extends AbstractAdminController
{
    /**
     * @return type
     */
    public function listAction()
    {
        $collection = $this->getDoctrine()->getRepository('MadefCmsBundle:Page')
                ->getDefaultCollection();

        $this->getDoctrine()->getRepository('MadefCmsBundle:Page')
                ->addVersions($collection);

        return $this->render('MadefCmsBundle:AdminPage:list.html.twig', array(
            'pages' => $collection,
        ));
    }

    /**
     * Display form to create a Page.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return type
     */
    public function addAction(Request $request)
    {
        $page = new Page();
        $page->setRemoved(false);

        $em = $this->getDoctrine()->getManager();

        $layoutList = $this->getDoctrine()->getRepository('MadefCmsBundle:Layout')
                ->getIdentifierList()->toArray();

        $form = $this->createFormBuilder($page)
            ->add('identifier', 'text', array(
                'attr' => array(
                    'placeholder' => 'home',
            ), ))
            ->add('layout_identifier', 'choice', array(
                'choices'   => $layoutList,
                'empty_value'  => '',
                'attr' => array(
                    'data-error-version-title' => $this->get('translator')->trans('admin.page.page.add.field.layoutidentifier.error.version.title'),
                    'data-error-version-content' => $this->get('translator')->trans('admin.page.page.add.field.layoutidentifier.error.version.content'),
                    'data-ajax-url' => $this->generateUrl('madef_cms_admin_ajax_render_structure'),
                ),
            ))
            ->add('content', new \Madef\CmsBundle\Form\Type\StructureType(), array(
                'attr' => array(
                    'data-ajax-render-content-url' => $this->generateUrl('madef_cms_admin_ajax_render_content'),
                    'data-ajax-render-widget-from-url' => $this->generateUrl('madef_cms_admin_ajax_render_widget_form'),
                ),
            ))
            ->add('version', 'entity', array(
                'class' => 'MadefCmsBundle:Version',
                'empty_value'  => '',
                'property' => 'identifier',
                'query_builder' => function (EntityRepository $er) {
                    return $er->getNotPublishedQuery();
                },
                'attr' => array(
                    'data-ajax-url' => $this->generateUrl('madef_cms_admin_ajax_widgets_and_layouts'),
                ),
            ))
            ->add('stay', 'submit', array(
                'label' => $this->get('translator')->trans('admin.form.button.stay'),
            ))
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($page);
            $em->flush();

            if ($form->get('stay')->isClicked()) {
                return $this->redirect($this->generateUrl('madef_cms_admin_page_edit', array(
                    'identifier' => $page->getIdentifier(),
                    'version' => $page->getVersion()->getId(),
                )));
            } else {
                return $this->redirect($this->generateUrl('madef_cms_admin_page_list'));
            }
        }

        return $this->render('MadefCmsBundle:Admin:form.html.twig', array(
            'form' => $form->createView(),
            'title' => $this->get('translator')->trans('admin.page.page.add.title'),
        ));
    }

    /**
     * Display form to edit a Page.
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

        $currentPage = $this->getDoctrine()->getRepository('MadefCmsBundle:Page')
                ->findOneBy(array('identifier' => $identifier, 'version' => $version));

        if (!$currentPage) {
            throw $this->createNotFoundException('error.entity.page.notfound');
        }

        $layoutList = $this->getDoctrine()->getRepository('MadefCmsBundle:Layout')
                ->getIdentifierList()->toArray();

        // Use new page if version is published
        if ($version->wasPublished()) {
            $page = new Page();
            $page->setIdentifier($currentPage->getIdentifier());
            $page->setContent($currentPage->getContent());
            $page->setLayoutIdentifier($currentPage->getLayoutIdentifier());
            $page->setRemoved(false);
        } else {
            $page = $currentPage;
        }

        $form = $this->createFormBuilder($page)
            ->add('layout_identifier', 'choice', array(
                'choices'   => $layoutList,
                'empty_value'  => '',
                'attr' => array(
                    'data-error-version-title' => $this->get('translator')->trans('admin.page.page.add.field.layoutidentifier.error.version.title'),
                    'data-error-version-content' => $this->get('translator')->trans('admin.page.page.add.field.layoutidentifier.error.version.content'),
                    'data-ajax-url' => $this->generateUrl('madef_cms_admin_ajax_render_structure'),
                ),
            ))
            ->add('content', new \Madef\CmsBundle\Form\Type\StructureType(), array(
                'attr' => array(
                    'data-ajax-render-content-url' => $this->generateUrl('madef_cms_admin_ajax_render_content'),
                    'data-ajax-render-widget-from-url' => $this->generateUrl('madef_cms_admin_ajax_render_widget_form'),
                ),
            ))
            ->add('version', 'entity', array(
                'class' => 'MadefCmsBundle:Version',
                'empty_value'  => '',
                'property' => 'identifier',
                'query_builder' => function (EntityRepository $er) {
                    return $er->getNotPublishedQuery();
                },
                'attr' => array(
                    'data-ajax-url' => $this->generateUrl('madef_cms_admin_ajax_widgets_and_layouts'),
                ),
            ))
            ->add('stay', 'submit', array(
                'label' => $this->get('translator')->trans('admin.form.button.stay'),
            ))
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($page);
            $em->flush();

            if ($form->get('stay')->isClicked()) {
                return $this->redirect($this->generateUrl('madef_cms_admin_page_edit', array(
                    'identifier' => $page->getIdentifier(),
                    'version' => $page->getVersion()->getId(),
                )));
            } else {
                return $this->redirect($this->generateUrl('madef_cms_admin_page_list'));
            }
        }

        return $this->render('MadefCmsBundle:Admin:form.html.twig', array(
            'form' => $form->createView(),
            'title' => $this->get('translator')->trans('admin.page.page.edit.title'),
        ));
    }
}
