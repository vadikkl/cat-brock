<?php

namespace Ewave\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Ewave\CoreBundle\Entity\Setting;
use Ewave\CoreBundle\Form\SettingType;
use Ewave\CoreBundle\Form\SearchType;

/**
 * History controller.
 *
 * @Route("/control/history")
 */
class HistoryController extends AdvancedController
{
    /**
     * Lists all History entities.
     *
     * @Route("/{page}", name="ewave_control_history", defaults={"page"=1}, requirements={"page" = "\d+"})
     * @Template("EwaveCoreBundle:History:index.html.twig")
     */
    public function indexAction(Request $request, $page = 1)
    {
        $historyRepository = $this->getHistoryRepository();
        $form = $this->createForm(new SearchType());
        $search = false;
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $search = $data['search'];
            }
        }
        $userId = $this->isAdmin() ? null : $this->getUser()->getId();


        $entities = $historyRepository->getList($userId, $search, (int)$page);
        $pages = $historyRepository->getListPages($userId, $search);

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
