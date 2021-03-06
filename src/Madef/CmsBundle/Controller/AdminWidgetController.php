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
use Madef\CmsBundle\Entity\Widget;
use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AdminWidgetController extends AbstractAdminController
{
    /**
     * @return type
     */
    public function listAction()
    {
        $collection = $this->getDoctrine()->getRepository('MadefCmsBundle:Widget')
                ->getDefaultCollection();

        $this->getDoctrine()->getRepository('MadefCmsBundle:Widget')
                ->addVersions($collection);

        return $this->render('MadefCmsBundle:AdminWidget:list.html.twig', array(
            'widgets' => $collection,
        ));
    }

    /**
     * Display form to create a Widget.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return type
     */
    public function addAction(Request $request)
    {
        $widget = new Widget();
        $widget->setRemoved(false);

        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder($widget)
            ->add('identifier', 'text', array(
                'attr' => array(
                    'placeholder' => 'widget-identifier',
            ), ))
            ->add('form', 'textarea', array(
                'attr' => array(
                    'placeholder' => '[{"title": "text"}, {"content": "textarea"}]',
            ), ))
            ->add('template', 'textarea', array(
                'attr' => array(
                    'placeholder' => '<h2>{{ title }}</h2><p>{{content}}</p>',
            ), ))
            ->add('default_content', 'textarea', array(
                'attr' => array(
                    'placeholder' => '{"title": "Hello", "content": "Hello world!"}',
            ), ))
            ->add('css', 'textarea', array(
                'required' => false,
            ))
            ->add('js', 'textarea', array(
                'required' => false,
            ))
            ->add('front_renderer', 'text', array(
                'attr' => array(
                    'placeholder' => '\Madef\CmsBundle\Renderer\DefaultFrontRenderer',
                    'value' => '\Madef\CmsBundle\Renderer\DefaultFrontRenderer',
            ), ))
            ->add('back_renderer', 'text', array(
                'attr' => array(
                    'placeholder' => '\Madef\CmsBundle\Renderer\DefaultBackRenderer',
                    'value' => '\Madef\CmsBundle\Renderer\DefaultBackRenderer',
            ), ))
            ->add('version', 'entity', array(
                'class' => 'MadefCmsBundle:Version',
                'empty_value'  => '',
                'property' => 'identifier',
                'query_builder' => function (EntityRepository $er) {
                    return $er->getNotPublishedQuery();
                },
            ))
            ->add('stay', 'submit', array(
                'label' => $this->get('translator')->trans('admin.form.button.stay'),
            ))
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($widget);
            $em->flush();

            if ($form->get('stay')->isClicked()) {
                return $this->redirect($this->generateUrl('madef_cms_admin_widget_edit', array(
                    'identifier' => $widget->getIdentifier(),
                    'version' => $widget->getVersion()->getId(),
                )));
            } else {
                return $this->redirect($this->generateUrl('madef_cms_admin_widget_list'));
            }
        }

        return $this->render('MadefCmsBundle:Admin:form.html.twig', array(
            'form' => $form->createView(),
            'title' => $this->get('translator')->trans('admin.widget.widget.add.title'),
        ));
    }

    /**
     * Display form to edit a Widget.
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

        $currentWidget = $this->getDoctrine()->getRepository('MadefCmsBundle:Widget')
                ->findOneBy(array('identifier' => $identifier, 'version' => $version));

        if (!$currentWidget) {
            throw $this->createNotFoundException($this->get('translator')->trans('admin.error.entity.widget.notfound'));
        }

        // Use new widget if version is published
        if ($version->wasPublished()) {
            $widget = new Widget();
            $widget->setIdentifier($currentWidget->getIdentifier());
            $widget->setForm($currentWidget->getForm());
            $widget->setTemplate($currentWidget->getTemplate());
            $widget->setDefaultContent($currentWidget->getDefaultContent());
            $widget->setFrontRenderer($currentWidget->getFrontRenderer());
            $widget->setBackRenderer($currentWidget->getBackRenderer());
            $widget->setRemoved(false);
        } else {
            $widget = $currentWidget;
        }

        $form = $this->createFormBuilder($widget)
            ->add('form', 'textarea', array(
                'attr' => array(
                    'placeholder' => '[{"title": "text"}, {"content": "textarea"}]',
            ), ))
            ->add('template', 'textarea', array(
                'attr' => array(
                    'placeholder' => '<h2>{{ title }}</h2><p>{{content}}</p>',
            ), ))
            ->add('default_content', 'textarea', array(
                'attr' => array(
                    'placeholder' => '{"title": "Hello", "content": "Hello world!"}',
            ), ))
            ->add('css', 'textarea', array(
                'required' => false,
            ))
            ->add('js', 'textarea', array(
                'required' => false,
            ))
            ->add('front_renderer', 'text', array(
                'attr' => array(
                    'placeholder' => '\Madef\CmsBundle\Renderer\DefaultFrontRenderer',
            ), ))
            ->add('back_renderer', 'text', array(
                'attr' => array(
                    'placeholder' => '\Madef\CmsBundle\Renderer\DefaultBackRenderer',
            ), ))
            ->add('version', 'entity', array(
                'class' => 'MadefCmsBundle:Version',
                'empty_value'  => '',
                'property' => 'identifier',
                'query_builder' => function (EntityRepository $er) {
                    return $er->getNotPublishedQuery();
                },
            ))
            ->add('stay', 'submit', array(
                'label' => $this->get('translator')->trans('admin.form.button.stay'),
            ))
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($widget);
            $em->flush();

            if ($form->get('stay')->isClicked()) {
                return $this->redirect($this->generateUrl('madef_cms_admin_widget_edit', array(
                    'identifier' => $widget->getIdentifier(),
                    'version' => $widget->getVersion()->getId(),
                )));
            } else {
                return $this->redirect($this->generateUrl('madef_cms_admin_widget_list'));
            }
        }

        return $this->render('MadefCmsBundle:Admin:form.html.twig', array(
            'form' => $form->createView(),
            'title' => $this->get('translator')->trans('admin.widget.widget.edit.title'),
        ));
    }
}
