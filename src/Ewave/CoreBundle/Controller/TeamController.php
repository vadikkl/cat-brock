<?php

namespace Ewave\CoreBundle\Controller;

use Ewave\CoreBundle\Form\TeamType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Ewave\CoreBundle\Form\SearchType;

/**
 * Team controller.
 *
 * @Route("/control/team")
 */
class TeamController extends AdvancedController
{
    /**
     * Lists all Team entities.
     *
     * @Route("/{page}", name="ewave_control_team", defaults={"page"=1}, requirements={"page" = "\d+"})
     * @Template("EwaveCoreBundle:Team:index.html.twig")
     */
    public function indexAction(Request $request, $page = 1)
    {
        $teamRepository = $this->getTeamRepository();
        $form = $this->createForm(new SearchType());
        $search = false;
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $search = $data['search'];
            }
        }

        $entities = $teamRepository->getList($search, (int)$page);
        $pages = $teamRepository->getListPages($search);

        return array(
            'entities' => $entities,
            'page' =>$page,
            'count' => $pages['count'],
            'limit' => $pages['limit'],
            'pageCount' => ceil($pages['count'] / $pages['limit']),
            'form' => $form->createView()
        );
    }

    /**
     * Creates a new Team entity.
     *
     * @Route("/create/", name="ewave_control_team_create")
     * @Template("EwaveCoreBundle:Team:create.html.twig")
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(new TeamType());
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $manager = $this->getTeamManager();
                if ($manager->save($data)) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        'Team was created successfully'
                    );
                    $this->get('session')->set('team', null);
                } else {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'Error while creating'
                    );
                }
                return $this->redirect($this->generateUrl('ewave_control_team'));
            }
        }
        return array(
            'form' => $form->createView()
        );
    }

    /**
     * Edit Team entity.
     *
     * @Route("/edit/{id}", name="ewave_control_team_edit")
     * @Template("EwaveCoreBundle:Team:edit.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $id = (int)$id;
        $form = $this->createForm(new TeamType());
        if ($id) {
            $teamRepository = $this->getTeamRepository();
            $team = $teamRepository->find($id);
            if ($team) {
                $form->get('title')->setData($team->getTitle());
                if ($request->getMethod() == 'POST') {
                    $form->handleRequest($request);
                    if ($form->isValid()) {
                        /* @var $data Team */
                        $data = $form->getData();
                        $teamManager = $this->getTeamManager();
                        if ($teamManager->update($data, $team)) {
                            $this->get('session')->getFlashBag()->add(
                                'success',
                                'Team was changed'
                            );
                            $this->get('session')->set('teams', null);
                        } else {
                            $this->get('session')->getFlashBag()->add(
                                'danger',
                                'Error while editing'
                            );
                        }
                        return $this->redirect($this->generateUrl('ewave_control_team'));
                    }
                }
            } else {
                return $this->entityNotFound();
            }
        } else {
            return $this->entityNotFound();
        }
        return array(
            'edit_form' => $form->createView()
        );
    }

    /**
     * Delete Team entity.
     *
     * @Route("/delete/{id}", name="ewave_control_team_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        $id = (int)$id;
        if ($id) {
            $teamRepository = $this->getTeamRepository();
            $team = $teamRepository->find($id);
            if ($team) {
                $teamManager = $this->getTeamManager();
                if ($teamManager->delete($team)) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        'Team was deleted'
                    );
                    $this->get('session')->set('teams', null);
                } else {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'Error while deleting'
                    );
                }
            } else {
                return $this->entityNotFound();
            }
        } else {
            return $this->entityNotFound();
        }
        return $this->redirect($this->generateUrl('ewave_control_team'));
    }

    protected function entityNotFound() {
        $this->get('session')->getFlashBag()->add(
            'warning',
            'Team not found'
        );
        return $this->redirect($this->generateUrl('ewave_control_team'));
    }
}
