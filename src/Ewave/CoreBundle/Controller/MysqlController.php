<?php

namespace Ewave\CoreBundle\Controller;

use Ewave\CoreBundle\Form\MysqlType;
use Ewave\CoreBundle\Service\Coder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Mysql controller.
 *
 * @Route("/control/sql")
 */
class MysqlController extends AdvancedController
{
    use Coder;
    /**
     * Creates a new Mysql entity.
     *
     * @Route("/create/project/{project}/environment/{environment}", name="ewave_control_mysql_create")
     * @Template("EwaveCoreBundle:Mysql:create.html.twig")
     */
    public function createAction(Request $request, $project, $environment)
    {
        $form = $this->createForm(new MysqlType());
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
                $manager = $this->getMysqlManager();
                if ($manager->create($data, $environmentEntity)) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        'SQL created successfully'
                    );
                    $this->get('session')->set('mysql', null);
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
     * Edit Mysql entity.
     *
     * @Route("/edit/{id}/project/{project}", name="ewave_control_mysql_edit")
     * @Template("EwaveCoreBundle:Mysql:edit.html.twig")
     */
    public function editAction(Request $request, $id, $project)
    {
        $id = (int)$id;
        $form = $this->createForm(new MysqlType());
        if ($id) {
            $mysqlRepository = $this->getMysqlRepository();
            $mysql = $mysqlRepository->find($id);
            if ($mysql) {
                $form->get('server')->setData($this->decodeValue($mysql->getServer()));
                $form->get('port')->setData($this->decodeValue($mysql->getPort()));
                $form->get('user')->setData($this->decodeValue($mysql->getUser()));
                $form->get('password')->setData($this->decodeValue($mysql->getPassword()));
                $form->get('dbname')->setData($this->decodeValue($mysql->getDbname()));
                if ($request->getMethod() == 'POST') {
                    $form->handleRequest($request);
                    if ($form->isValid()) {
                        $data = $form->getData();
                        $mysqlManager = $this->getMysqlManager();
                        if ($mysqlManager->update($data, $mysql)) {
                            $this->get('session')->getFlashBag()->add(
                                'success',
                                'SQL edited successfully'
                            );
                            $this->get('session')->set('mysql', null);
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
     * Delete Mysql entity.
     *
     * @Route("/delete/{id}/project/{project}", name="ewave_control_mysql_delete")
     */
    public function deleteAction(Request $request, $id, $project)
    {
        $id = (int)$id;
        if ($id) {
            $mysqlRepository = $this->getMysqlRepository();
            $mysql = $mysqlRepository->find($id);
            if ($mysql) {
                $mysqlManager = $this->getMysqlManager();
                if ($mysqlManager->delete($mysql)) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        'SQL deleted successfully'
                    );
                    $this->get('session')->set('mysql', null);
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
            'SQL not found'
        );
        return $this->redirect($this->generateUrl('ewave_control_project'));
    }
}
