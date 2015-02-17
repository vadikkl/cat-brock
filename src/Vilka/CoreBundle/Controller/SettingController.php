<?php

namespace Vilka\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Vilka\CoreBundle\Entity\Subway;
use Vilka\CoreBundle\Form\SubwayType;
use Vilka\CoreBundle\Form\SearchType;

/**
 * Subway controller.
 *
 * @Route("/control/setting")
 */
class SettingController extends AdvancedController
{
    /**
     * Lists all Setting entities.
     *
     * @Route("/{page}", name="vilka_control_setting", defaults={"page"=1}, requirements={"page" = "\d+"})
     * @Template("VilkaCoreBundle:Setting:index.html.twig")
     */
    public function indexAction(Request $request, $page = 1)
    {
        $subwayRepository = $this->getSubwayRepository();
        $form = $this->createForm(new SearchType());
        $search = false;
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $search = $data['search'];
            }
        }

        $entities = $subwayRepository->getList($search, (int)$page);
        $pages = $subwayRepository->getListPages($search);

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
