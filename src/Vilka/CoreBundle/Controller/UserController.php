<?php

namespace Vilka\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Vilka\CoreBundle\Entity\Setting;
use Vilka\CoreBundle\Form\SettingType;
use Vilka\CoreBundle\Form\SearchType;

/**
 * User controller.
 *
 * @Route("/control/user")
 */
class UserController extends AdvancedController
{
    /**
     * Lists all Setting entities.
     *
     * @Route("/{page}", name="vilka_control_user", defaults={"page"=1}, requirements={"page" = "\d+"})
     * @Template("VilkaCoreBundle:User:index.html.twig")
     */
    public function indexAction(Request $request, $page = 1)
    {
        $userRepository = $this->getUserRepository();
        $form = $this->createForm(new SearchType());
        $search = false;
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $search = $data['search'];
            }
        }

        $entities = $userRepository->getList($search, (int)$page);
        $pages = $userRepository->getListPages($search);

        return array(
            'entities' => $entities,
            'page' =>$page,
            'count' => $pages['count'],
            'limit' => $pages['limit'],
            'pageCount' => ceil($pages['count'] / $pages['limit']),
            'form' => $form->createView()
        );
    }
}
