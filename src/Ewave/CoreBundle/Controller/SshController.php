<?php

namespace Ewave\CoreBundle\Controller;

use Ewave\CoreBundle\Form\SshType;
use Ewave\CoreBundle\Service\Coder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Ssh controller.
 *
 * @Route("/control/ssh")
 */
class SshController extends AdvancedController
{
    use Coder;
    /**
     * Creates a new Ssh entity.
     *
     * @Route("/create/project/{project}/environment/{environment}", name="ewave_control_ssh_create")
     * @Template("EwaveCoreBundle:Ssh:create.html.twig")
     */
    public function createAction(Request $request, $project, $environment)
    {
        $form = $this->createForm(new SshType());
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
                $manager = $this->getSshManager();
                if ($manager->create($data, $environmentEntity)) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        'Ssh created successfully'
                    );
                    $this->get('session')->set('ssh', null);
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
     * @Route("/edit/{id}/project/{project}", name="ewave_control_ssh_edit")
     * @Template("EwaveCoreBundle:Ssh:edit.html.twig")
     */
    public function editAction(Request $request, $id, $project)
    {
        $id = (int)$id;
        $form = $this->createForm(new SshType());
        if ($id) {
            $sshRepository = $this->getSshRepository();
            $ssh = $sshRepository->find($id);
            if ($ssh) {
                $form->get('server')->setData($this->decodeValue($ssh->getServer()));
                $form->get('port')->setData($this->decodeValue($ssh->getPort()));
                $form->get('user')->setData($this->decodeValue($ssh->getUser()));
                $form->get('password')->setData($this->decodeValue($ssh->getPassword()));
                $form->get('description')->setData($this->decodeValue($ssh->getDescription()));
                if ($request->getMethod() == 'POST') {
                    $form->handleRequest($request);
                    if ($form->isValid()) {
                        $data = $form->getData();
                        $sshManager = $this->getSshManager();
                        if ($sshManager->update($data, $ssh)) {
                            $this->get('session')->getFlashBag()->add(
                                'success',
                                'SSH edited successfully'
                            );
                            $this->get('session')->set('ssh', null);
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
     * @Route("/delete/{id}/project/{project}", name="ewave_control_ssh_delete")
     */
    public function deleteAction(Request $request, $id, $project)
    {
        $id = (int)$id;
        if ($id) {
            $sshRepository = $this->getSshRepository();
            $ssh = $sshRepository->find($id);
            if ($ssh) {
                $sshManager = $this->getSshManager();
                if ($sshManager->delete($ssh)) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        'SSH deleted successfully'
                    );
                    $this->get('session')->set('ssh', null);
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
            'Ssh not found'
        );
        return $this->redirect($this->generateUrl('ewave_control_project'));
    }
}
