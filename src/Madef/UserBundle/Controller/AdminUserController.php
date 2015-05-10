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

namespace Madef\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Madef\CmsBundle\Controller\AbstractAdminController;
use Madef\CmsBundle\Event\RoleEvent;

class AdminUserController extends AbstractAdminController
{
    /**
     * @return type
     */
    public function listAction()
    {
        $userManager = $this->get('fos_user.user_manager');

        return $this->render('MadefUserBundle:AdminUser:list.html.twig', array(
            'users' => $userManager->findUsers(),
            'roles' => $this->getRoles(),
        ));
    }

    /**
     * Display form to create a User.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return type
     */
    public function addAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder($user)
            ->add('username', 'text')
            ->add('email', 'email')
            ->add('plain_password', 'password', array(
                'label' => $this->get('translator')->trans('admin.user.field.password'),
            ))
            ->add('roles', 'choice', array(
                'choices' => $this->getRoles(),
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ))
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $user->setEnabled(true);
            $em->persist($user);
            if (!empty($user->getPlainPassword())) {
                $this->get('fos_user.user_manager')->updatePassword($user, $user->getPlainPassword());
            }
            $em->flush();

            return $this->redirect($this->generateUrl('madef_user_admin_user_list'));
        }

        return $this->render('MadefUserBundle:Admin:form.html.twig', array(
            'form' => $form->createView(),
            'title' => $this->get('translator')->trans('admin.user.page.add.title'),
        ));
    }

    /**
     * Display form to edit a User.
     *
     * @ParamConverter("user", class="MadefUserBundle:User")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \FOS\UserBundle\Model\User                $user
     *
     * @return type
     */
    public function editAction(Request $request, \FOS\UserBundle\Model\User $user)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder($user)
            ->add('username', 'text')
            ->add('email', 'email')
            ->add('plain_password', 'password', array(
                'label' => $this->get('translator')->trans('admin.user.field.password'),
                'required' => false,
            ))
            ->add('roles', 'choice', array(
                'choices' => $this->getRoles(),
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ))
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $user->setEnabled(true);
            $em->persist($user);
            if (!empty($user->getPlainPassword())) {
                $this->get('fos_user.user_manager')->updatePassword($user, $user->getPlainPassword());
            }
            $em->flush();

            return $this->redirect($this->generateUrl('madef_user_admin_user_list'));
        }

        return $this->render('MadefUserBundle:Admin:form.html.twig', array(
            'form' => $form->createView(),
            'title' => $this->get('translator')->trans('admin.user.page.edit.title'),
        ));
    }

    protected function getRoles()
    {
        $dispatcher = $this->get('event_dispatcher');
        $event = new RoleEvent();
        $dispatcher->dispatch('roles', $event);

        $roles = $event->getRoles();
        foreach ($roles as &$role) {
            $role = $this->get('translator')->trans($role, array(), 'role');
        }

        return $roles;
    }
}
