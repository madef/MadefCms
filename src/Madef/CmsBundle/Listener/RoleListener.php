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

class RoleListener
{
    public function addRoles(Event $event)
    {
        $event->addRole('ROLE_PAGE_VIEW', 'role.page.view');
        $event->addRole('ROLE_PAGE_EDIT', 'role.page.edit');
        $event->addRole('ROLE_LAYOUT', 'role.layout');
        $event->addRole('ROLE_WIDGET', 'role.widget');
        $event->addRole('ROLE_MEDIA', 'role.media');
        $event->addRole('ROLE_VERSION_VIEW', 'role.version.view');
        $event->addRole('ROLE_VERSION_EDIT', 'role.version.edit');
        $event->addRole('ROLE_SUPER_ADMIN', 'role.super_admin');
    }
}
