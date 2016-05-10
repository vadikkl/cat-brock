<?php

namespace Ewave\CoreBundle\Controller;

use Ewave\CoreBundle\Entity\Project;
use Ewave\CoreBundle\Form\ProjectType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Ewave\CoreBundle\Form\SearchType;

/**
 * Project controller.
 *
 * @Route("/control/project")
 */
class ProjectController extends AdvancedController
{
    /**
     * Lists all Project entities.
     *
     * @Route("/{page}", name="ewave_control_project", defaults={"page"=1}, requirements={"page" = "\d+"})
     * @Template("EwaveCoreBundle:Project:index.html.twig")
     */
    public function indexAction(Request $request, $page = 1)
    {
        $projectRepository = $this->getProjectRepository();
        $form = $this->createForm(new SearchType());
        $search = false;
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $search = $data['search'];
            }
        }

        $entities = $projectRepository->getList($search, (int)$page);
        $pages = $projectRepository->getListPages($search);

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
     * Creates a new Project entity.
     *
     * @Route("/create/", name="ewave_control_project_create")
     * @Template("EwaveCoreBundle:Project:create.html.twig")
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(new ProjectType());
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $manager = $this->getProjectManager();
                if ($manager->save($data)) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        'Project was created successfully'
                    );
                    $this->get('session')->set('settings', null);
                } else {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'Error while creating'
                    );
                }
                return $this->redirect($this->generateUrl('ewave_control_project'));
            }
        }
        return array(
            'form' => $form->createView()
        );
    }

    /**
     * Edit Project entity.
     *
     * @Route("/edit/{id}", name="ewave_control_project_edit")
     * @Template("EwaveCoreBundle:Project:edit.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $id = (int)$id;
        $form = $this->createForm(new ProjectType());
        if ($id) {
            $projectRepository = $this->getProjectRepository();
            $project = $projectRepository->find($id);
            if ($project) {
                $form->get('title')->setData($project->getTitle());
                $form->get('description')->setData($project->getDescription());
                if ($request->getMethod() == 'POST') {
                    $form->handleRequest($request);
                    if ($form->isValid()) {
                        /* @var $data Project */
                        $data = $form->getData();
                        $projectManager = $this->getProjectManager();
                        if ($projectManager->update($data, $project)) {
                            $this->get('session')->getFlashBag()->add(
                                'success',
                                'Project was changed successfully'
                            );
                            $this->get('session')->set('projects', null);
                        } else {
                            $this->get('session')->getFlashBag()->add(
                                'danger',
                                'Error while editing'
                            );
                        }
                        return $this->redirect($this->generateUrl('ewave_control_project'));
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
     * Delete Project entity.
     *
     * @Route("/delete/{id}", name="ewave_control_project_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        $id = (int)$id;
        if ($id) {
            $projectRepository = $this->getProjectRepository();
            $project = $projectRepository->find($id);
            if ($project) {
                $projectManager = $this->getProjectManager();
                if ($projectManager->delete($project)) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        'Project was deleted successfully'
                    );
                    $this->get('session')->set('projects', null);
                } else {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'Error while saving'
                    );
                }
            } else {
                return $this->entityNotFound();
            }
        } else {
            return $this->entityNotFound();
        }
        return $this->redirect($this->generateUrl('ewave_control_project'));
    }

    protected function entityNotFound() {
        $this->get('session')->getFlashBag()->add(
            'warning',
            'Project does not found'
        );
        return $this->redirect($this->generateUrl('ewave_control_project'));
    }
}
