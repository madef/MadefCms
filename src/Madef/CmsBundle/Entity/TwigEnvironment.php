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

class TwigEnvironment extends \Twig_Environment
{
    protected $controller;

    public function __construct($controller)
    {
        parent::__construct(new \Twig_Loader_String());

        $this->controller = $controller;

        $this->addFunction(new \Twig_SimpleFunction('url', function ($url, $params) {
                return $this->controller->generateUrl($url, $params);
        }));

        $this->addFilter(new \Twig_SimpleFilter('markdown', function ($md) {
            $parser = new \MarkdownExtended\Parser();

            return $parser->transform($md);
        }));

        $this->addFunction(new \Twig_SimpleFunction('media', function ($identifier) {
            return $this->controller->generateUrl('madef_cms_front_display_media', array(
                'identifier' => $identifier,
                'version' => $this->controller->getVersion()->getId(),
            ));
        }));

        $this->addFunction(new \Twig_SimpleFunction('link', function ($identifier) {
            if ($this->controller->getHashVersion() != 'current') {
                return $this->controller->generateUrl('madef_cms_front_display_versioned_page', array(
                    'identifier' => $identifier,
                    'hashVersion' => $this->controller->getHashVersion(),
                ));
            } else {
                return $this->controller->generateUrl('madef_cms_front_display_page', array(
                    'identifier' => $identifier,
                ));
            }
        }));
    }
}
