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

namespace Madef\CmsBundle\Listener;

use Symfony\Component\EventDispatcher\Event;

class MenuListener
{
    public function addToMenuLeft(Event $event)
    {
        $event->addEntry('cms', 'left_menu.cms');
        $event->addEntry('cms/page_list', 'left_menu.page.list', 'madef_cms_admin_page_list', 'ROLE_PAGE_VIEW');
        $event->addEntry('cms/page_add', 'left_menu.page.add', 'madef_cms_admin_page_add', 'ROLE_PAGE_EDIT');
        $event->addEntry('cms/layout_list', 'left_menu.layout.list', 'madef_cms_admin_layout_list', 'ROLE_LAYOUT');
        $event->addEntry('cms/layout_add', 'left_menu.layout.add', 'madef_cms_admin_layout_add', 'ROLE_LAYOUT');
        $event->addEntry('cms/widget_list', 'left_menu.widget.list', 'madef_cms_admin_widget_list', 'ROLE_WIDGET');
        $event->addEntry('cms/widget_add', 'left_menu.widget.add', 'madef_cms_admin_widget_add', 'ROLE_WIDGET');
        $event->addEntry('cms/media_list', 'left_menu.media.list', 'madef_cms_admin_media_list', 'ROLE_MEDIA');
        $event->addEntry('cms/media_add', 'left_menu.media.add', 'madef_cms_admin_media_add', 'ROLE_MEDIA');
        $event->addEntry('cms/version_list', 'left_menu.version.list', 'madef_cms_admin_version_list', 'ROLE_VERSION_VIEW');
        $event->addEntry('cms/version_add', 'left_menu.version.add', 'madef_cms_admin_version_add', 'ROLE_VERSION_EDIT');
    }
}
