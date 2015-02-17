<?php

namespace Vilka\CoreBundle\Controller;

use Ringbe\Bundle\CoreBundle\Entity\Setting;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Vilka\CoreBundle\Controller\AdvancedController;
use Vilka\CoreBundle\Form\AdminType;

class AdminController extends AdvancedController
{
    /**
     * @Route("/control", name="vilka_control_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/login", name="vilka_control_login")
     * @Template()
     */
    public function loginAction()
    {

        if ($this->get('request')->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $this->get('request')->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $this->get('request')->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('VilkaCoreBundle:Admin:login.html.twig', array(
            'last_username' => $this->get('request')->getSession()->get(SecurityContext::LAST_USERNAME),
            'error' => $error
        ));
    }

    /**
     * @Route("/control/profile", name="vilka_control_profile")
     * @Template()
     */
    public function profileAction()
    {
        return array();
    }

    /**
     * @Route("/control/profile/edit", name="vilka_control_profile_edit")
     * @Template()
     */
    public function profile_editAction(Request $request)
    {
        $form = $this->createForm(new AdminType());
        $form->get('username')->setData($this->getUser()->getUsername());
        $form->get('email')->setData($this->getUser()->getEmail());
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                if ($data['password'] === $data['password_confirm']) {
                    $userManager = $this->getUserManager();
                    if ($userManager->update($data, $this->getUser())) {
                        $this->get('session')->getFlashBag()->add(
                            'success',
                            'Профиль успешно сохранен'
                        );
                    }
                    return $this->redirect($this->generateUrl('vilka_control_profile'));

                }
                $this->get('session')->getFlashBag()->add(
                    'danger',
                    'Пароли не совпадают'
                );
            }
        }
        return array(
            'form' => $form->createView()
        );
    }
}