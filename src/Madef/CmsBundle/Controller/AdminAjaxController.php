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
use Madef\CmsBundle\Entity\Layout;
use Madef\CmsBundle\Entity\Widget;
use Symfony\Component\HttpFoundation\Response;

class AdminAjaxController extends Controller
{
    /**
     * Display form to create a Layout
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function widgetsAndLayoutsAction(Request $request)
    {
        $version = $this->getDoctrine()->getRepository('MadefCmsBundle:Version')
                ->findOneByIdentifier($request->get('version'));

        $layoutCollection = $this->getDoctrine()->getRepository('MadefCmsBundle:Layout')
                ->findByVersion($version);

        $layoutList = array();
        foreach ($layoutCollection as $layout) {
            $layoutList[$layout->getIdentifier()] = array(
                'identifier' => $layout->getIdentifier(),
                'default_content' => json_decode($layout->getDefaultContent()),
                'structure' => json_decode($layout->getStructure()),
            );
        }

        $widgetCollection = $this->getDoctrine()->getRepository('MadefCmsBundle:Widget')
                ->findByVersion($version);

        $widgetList = array();
        foreach ($widgetCollection as $widget) {
            $widgetList[$widget->getIdentifier()] = array(
                'identifier' => $widget->getIdentifier(),
                'default_content' => json_decode($widget->getDefaultContent()),
                'form' => json_decode($widget->getForm()),
            );
        }

        return new Response(json_encode(array(
            'layoutList' => $layoutList,
            'widgetList' => $widgetList,
            'widgetListHtml' => $this->renderView('MadefCmsBundle:AdminAjax:widget-list.html.twig', array('widgetList' => $widgetList))
        )));
    }

    /**
     * Render content of the stucture (drag & dropable items)
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function renderContentAction(Request $request)
    {
        $content = $request->get('content');

        $contentList = array();

        foreach ($content as $blockIdentifier => $blockData) {
            $contentList[$blockIdentifier] = array();
            foreach ($blockData as $widgetData) {
                if (!isset($widgetData['vars'])) {
                    $widgetData['vars'] = array();
                }
                $contentList[$blockIdentifier][] = $this->renderView('MadefCmsBundle:AdminAjax:widget-item.html.twig',
                        array(
                            'widget' => $widgetData['identifier'],
                            'vars' => json_encode($widgetData['vars']),
                        )
                    );
            }
        }

        return new Response(json_encode($contentList));
    }
    /**
     * Render widget form
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function renderWidgetFormAction(Request $request)
    {
        $formatedFields = array();
        $identifier = $request->get('identifier');
        $vars = $request->get('vars');
        $fields = $request->get('fields');

        if (!is_array($fields)) {
            $fields = array();
        }

        foreach ($fields as $field) {
            foreach ($field as $fieldName => $type) {
                $formatedFields[] = array(
                    'type' => $type,
                    'name' => $fieldName,
                    'label' => $this->get('translator')->trans('admin.widget.type.' . $identifier . '.label.' . $fieldName),
                    'value' => (!empty($vars[$fieldName])) ? $vars[$fieldName] : '',
                );
            }
        }

        return $this->render('MadefCmsBundle:AdminAjax:widget-form.html.twig', array(
            'widgetName' => $identifier,
            'fields' => $formatedFields,
        ));
    }

    /**
     * Render structure of a layout
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function renderStructureAction(Request $request)
    {
        return $this->render('MadefCmsBundle:AdminLayout:structure.html.twig', array(
            'structure' => $request->get('structure'),
        ));
    }
}
