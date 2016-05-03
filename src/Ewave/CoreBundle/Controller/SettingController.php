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
 * Setting controller.
 *
 * @Route("/control/setting")
 */
class SettingController extends AdvancedController
{
    /**
     * Lists all Setting entities.
     *
     * @Route("/{page}", name="ewave_control_setting", defaults={"page"=1}, requirements={"page" = "\d+"})
     * @Template("EwaveCoreBundle:Setting:index.html.twig")
     */
    public function indexAction(Request $request, $page = 1)
    {
        $settingRepository = $this->getSettingRepository();
        $form = $this->createForm(new SearchType());
        $search = false;
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $search = $data['search'];
            }
        }

        $entities = $settingRepository->getList($search, (int)$page);
        $pages = $settingRepository->getListPages($search);

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
     * Creates a new Setting entity.
     *
     * @Route("/create/", name="ewave_control_setting_create")
     * @Template("EwaveCoreBundle:Setting:create.html.twig")
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(new SettingType());
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $manager = $this->getSettingManager();
                if ($manager->save($data)) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        'Настройка успешно создана'
                    );
                    $this->get('session')->set('settings', null);
                } else {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'Ошибка при создании'
                    );
                }
                return $this->redirect($this->generateUrl('ewave_control_setting'));
            }
        }
        return array(
            'form' => $form->createView()
        );
    }

    /**
     * Edit Setting entity.
     *
     * @Route("/edit/{id}", name="ewave_control_setting_edit")
     * @Template("EwaveCoreBundle:Setting:edit.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $id = (int)$id;
        $form = $this->createForm(new SettingType());
        if ($id) {
            $settingRepository = $this->getSettingRepository();
            $setting = $settingRepository->find($id);
            if ($setting) {
                $form->get('name')->setData($setting->getName());
                $form->get('value')->setData($setting->getValue());
                $form->get('description')->setData($setting->getDescription());
                if ($request->getMethod() == 'POST') {
                    $form->handleRequest($request);
                    if ($form->isValid()) {
                        /* @var $data Setting */
                        $data = $form->getData();
                        $settingManager = $this->getSettingManager();
                        if ($settingManager->update($data, $setting)) {
                            $this->get('session')->getFlashBag()->add(
                                'success',
                                'Настройка успешно изменена'
                            );
                            $this->get('session')->set('settings', null);
                        } else {
                            $this->get('session')->getFlashBag()->add(
                                'danger',
                                'Ошибка при редактировании'
                            );
                        }
                        return $this->redirect($this->generateUrl('ewave_control_setting'));
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
     * @Route("/delete/{id}", name="ewave_control_setting_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        $id = (int)$id;
        if ($id) {
            $settingRepository = $this->getSettingRepository();
            $setting = $settingRepository->find($id);
            if ($setting) {
                $settingManager = $this->getSettingManager();
                if ($settingManager->delete($setting)) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        'Настройка успешно удалена'
                    );
                    $this->get('session')->set('settings', null);
                } else {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'Ошибка при удалении'
                    );
                }
            } else {
                return $this->entityNotFound();
            }
        } else {
            return $this->entityNotFound();
        }
        return $this->redirect($this->generateUrl('ewave_control_setting'));
    }

    protected function entityNotFound() {
        $this->get('session')->getFlashBag()->add(
            'warning',
            'Настройка не найдена'
        );
        return $this->redirect($this->generateUrl('ewave_control_setting'));
    }
}
