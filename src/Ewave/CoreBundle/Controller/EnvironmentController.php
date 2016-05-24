<?php

namespace Ewave\CoreBundle\Controller;

use Ewave\CoreBundle\Entity\Environment;
use Ewave\CoreBundle\Form\EnvironmentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Ewave\CoreBundle\Form\SearchType;

/**
 * Environment controller.
 *
 * @Route("/control/environment")
 */
class EnvironmentController extends AdvancedController
{
    /**
     * Creates a new Environment entity.
     *
     * @Route("/create/project/{project}", name="ewave_control_environment_create")
     * @Template("EwaveCoreBundle:Environment:create.html.twig")
     */
    public function createAction(Request $request, $project)
    {
        $form = $this->createForm(new EnvironmentType());
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $projectEntity = $this->getProjectRepository()->find($project);
                if (!$projectEntity) {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'The project does not exist'
                    );
                    return $this->redirect($this->generateUrl('ewave_control_project_view', array('id' => $project)));
                }
                $manager = $this->getEnvironmentManager();
                if ($manager->create($data, $projectEntity)) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        'Environment crated successfully'
                    );
                    $this->get('session')->set('settings', null);
                } else {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'Error while creating'
                    );
                }
                return $this->redirect($this->generateUrl('ewave_control_project_view', array('id' => $project)));
            }
        }
        return array(
            'form' => $form->createView()
        );
    }

    /**
     * Edit Environment entity.
     *
     * @Route("/edit/{id}", name="ewave_control_environment_edit")
     * @Template("EwaveCoreBundle:Environment:edit.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $id = (int)$id;
        $form = $this->createForm(new EnvironmentType());
        if ($id) {
            $environmentRepository = $this->getEnvironmentRepository();
            $environment = $environmentRepository->find($id);
            if ($environment) {
                $form->get('type')->setData($environment->getType());
                $form->get('description')->setData($environment->getDescription());
                if ($request->getMethod() == 'POST') {
                    $form->handleRequest($request);
                    if ($form->isValid()) {
                        /* @var $data Environment */
                        $data = $form->getData();
                        $environmentManager = $this->getEnvironmentManager();
                        if ($environmentManager->update($data, $environment)) {
                            $this->get('session')->getFlashBag()->add(
                                'success',
                                'Environment edited successfully'
                            );
                            $this->get('session')->set('environments', null);
                        } else {
                            $this->get('session')->getFlashBag()->add(
                                'danger',
                                'Error while editing'
                            );
                        }
                        return $this->redirect($this->generateUrl('ewave_control_project_view', array('id' => $environment->getProject()->getId())));
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
     * Delete Setting entity.
     *
     * @Route("/delete/{id}", name="ewave_control_environment_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        $id = (int)$id;
        if ($id) {
            $environmentRepository = $this->getEnvironmentRepository();
            $environment = $environmentRepository->find($id);
            if ($environment) {
                $environmentManager = $this->getEnvironmentManager();
                if ($environmentManager->delete($environment)) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        'Environment deleted successfully'
                    );
                    $this->get('session')->set('environments', null);
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
        return $this->redirect($this->generateUrl('ewave_control_project_view', array('id' => $environment->getProject()->getId())));
    }

    protected function entityNotFound() {
        $this->get('session')->getFlashBag()->add(
            'warning',
            'Environment not found'
        );
        return $this->redirect($this->generateUrl('ewave_control_project'));
    }
}
