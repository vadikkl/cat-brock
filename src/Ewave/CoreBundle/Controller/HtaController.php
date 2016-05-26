<?php

namespace Ewave\CoreBundle\Controller;

use Ewave\CoreBundle\Form\HtaType;
use Ewave\CoreBundle\Service\Coder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Hta controller.
 *
 * @Route("/control/hta")
 */
class HtaController extends AdvancedController
{
    use Coder;
    /**
     * Creates a new Hta entity.
     *
     * @Route("/create/project/{project}/environment/{environment}", name="ewave_control_hta_create")
     * @Template("EwaveCoreBundle:Hta:create.html.twig")
     */
    public function createAction(Request $request, $project, $environment)
    {
        $form = $this->createForm(new HtaType());
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
                $manager = $this->getHtaManager();
                if ($manager->create($data, $environmentEntity)) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        'Hta created successfully'
                    );
                    $this->get('session')->set('hta', null);
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
     * @Route("/edit/{id}/project/{project}", name="ewave_control_hta_edit")
     * @Template("EwaveCoreBundle:Hta:edit.html.twig")
     */
    public function editAction(Request $request, $id, $project)
    {
        $id = (int)$id;
        $form = $this->createForm(new HtaType());
        if ($id) {
            $htaRepository = $this->getHtaRepository();
            $hta = $htaRepository->find($id);
            if ($hta) {
                $form->get('user')->setData($this->decodeValue($hta->getUser()));
                $form->get('password')->setData($this->decodeValue($hta->getPassword()));
                if ($request->getMethod() == 'POST') {
                    $form->handleRequest($request);
                    if ($form->isValid()) {
                        $data = $form->getData();
                        $htaManager = $this->getHtaManager();
                        if ($htaManager->update($data, $hta)) {
                            $this->get('session')->getFlashBag()->add(
                                'success',
                                'SSH edited successfully'
                            );
                            $this->get('session')->set('hta', null);
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
     * @Route("/delete/{id}/project/{project}", name="ewave_control_hta_delete")
     */
    public function deleteAction(Request $request, $id, $project)
    {
        $id = (int)$id;
        if ($id) {
            $htaRepository = $this->getHtaRepository();
            $hta = $htaRepository->find($id);
            if ($hta) {
                $htaManager = $this->getHtaManager();
                if ($htaManager->delete($hta)) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        'SSH deleted successfully'
                    );
                    $this->get('session')->set('hta', null);
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
            'Hta not found'
        );
        return $this->redirect($this->generateUrl('ewave_control_project'));
    }
}
