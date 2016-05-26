<?php

namespace Ewave\CoreBundle\Controller;

use Ewave\CoreBundle\Form\OfficeType;
use Ewave\CoreBundle\Service\Coder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Office controller.
 *
 * @Route("/control/office")
 */
class OfficeController extends AdvancedController
{
    use Coder;
    /**
     * Creates a new Office entity.
     *
     * @Route("/create/project/{project}/environment/{environment}", name="ewave_control_office_create")
     * @Template("EwaveCoreBundle:Office:create.html.twig")
     */
    public function createAction(Request $request, $project, $environment)
    {
        $form = $this->createForm(new OfficeType());
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $environmentEntity = $this->getEnvironmentRepository()->find($environment);
                if (!$environmentEntity) {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'The environment does not exist'
                    );
                    return $this->redirect($this->generateUrl('ewave_control_project_view', array('id' => $project)));
                }
                $manager = $this->getOfficeManager();
                if ($manager->create($data, $environmentEntity)) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        'Office created successfully'
                    );
                    $this->get('session')->set('office', null);
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
     * @Route("/edit/{id}/project/{project}", name="ewave_control_office_edit")
     * @Template("EwaveCoreBundle:Office:edit.html.twig")
     */
    public function editAction(Request $request, $id, $project)
    {
        $id = (int)$id;
        $form = $this->createForm(new OfficeType());
        if ($id) {
            $officeRepository = $this->getOfficeRepository();
            $office = $officeRepository->find($id);
            if ($office) {
                $form->get('url')->setData($this->decodeValue($office->getUrl()));
                $form->get('user')->setData($this->decodeValue($office->getUser()));
                $form->get('password')->setData($this->decodeValue($office->getPassword()));
                if ($request->getMethod() == 'POST') {
                    $form->handleRequest($request);
                    if ($form->isValid()) {
                        $data = $form->getData();
                        $officeManager = $this->getOfficeManager();
                        if ($officeManager->update($data, $office)) {
                            $this->get('session')->getFlashBag()->add(
                                'success',
                                'SSH edited successfully'
                            );
                            $this->get('session')->set('office', null);
                        } else {
                            $this->get('session')->getFlashBag()->add(
                                'danger',
                                'Error while editing'
                            );
                        }
                        return $this->redirect($this->generateUrl('ewave_control_project_view', array('id' => $project)));
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
     * @Route("/delete/{id}/project/{project}", name="ewave_control_office_delete")
     */
    public function deleteAction(Request $request, $id, $project)
    {
        $id = (int)$id;
        if ($id) {
            $officeRepository = $this->getOfficeRepository();
            $office = $officeRepository->find($id);
            if ($office) {
                $officeManager = $this->getOfficeManager();
                if ($officeManager->delete($office)) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        'SSH deleted successfully'
                    );
                    $this->get('session')->set('office', null);
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
        return $this->redirect($this->generateUrl('ewave_control_project_view', array('id' => $project)));
    }

    protected function entityNotFound() {
        $this->get('session')->getFlashBag()->add(
            'warning',
            'Office not found'
        );
        return $this->redirect($this->generateUrl('ewave_control_project'));
    }
}
