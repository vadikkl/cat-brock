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
 * @Route("/control/subway")
 */
class SubwayController extends AdvancedController
{
    /**
     * Lists all Subway entities.
     *
     * @Route("/{page}", name="vilka_control_subway", defaults={"page"=1}, requirements={"page" = "\d+"})
     * @Template("VilkaCoreBundle:Subway:index.html.twig")
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

    /**
     * View Subway entity.
     *
     * @Route("/view/{id}", name="vilka_control_subway_view")
     * @Template("VilkaCoreBundle:Subway:view.html.twig")
     */
    public function viewAction(Request $request, $id)
    {
        $id = (int)$id;
        if ($id) {
            $subwayRepository = $this->getSubwayRepository();
            $subway = $subwayRepository->find($id);
            if ($subway) {
                return array(
                    'entity' => $subway
                );
            } else {
                return $this->entityNotFound();
            }
        } else {
            return $this->entityNotFound();
        }
    }


    /**
     * Creates a new Subway entity.
     *
     * @Route("/create/", name="vilka_control_subway_create")
     * @Template("VilkaCoreBundle:Subway:create.html.twig")
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(new SubwayType());
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $manager = $this->getSubwayManager();
                if ($manager->save($data)) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        'Станция метро успешно создана'
                    );
                } else {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'Ошибка при создании'
                    );
                }
                return $this->redirect($this->generateUrl('vilka_control_subway'));
            }
        }
        return array(
            'form' => $form->createView()
        );
    }

    /**
     * Edit Subway entity.
     *
     * @Route("/edit/{id}", name="vilka_control_subway_edit")
     * @Template("VilkaCoreBundle:Subway:edit.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $id = (int)$id;
        $form = $this->createForm(new SubwayType());
        if ($id) {
            $subwayRepository = $this->getSubwayRepository();
            $subway = $subwayRepository->find($id);
            if ($subway) {
                $form->get('name')->setData($subway->getName());
                $form->get('line')->setData($subway->getLine());
                if ($request->getMethod() == 'POST') {
                    $form->handleRequest($request);
                    if ($form->isValid()) {
                        /* @var $data Subway*/
                        $data = $form->getData();
                        $subwayManager = $this->getSubwayManager();
                        if ($subwayManager->update($data, $subway)) {
                            $this->get('session')->getFlashBag()->add(
                                'success',
                                'Станция метро успешно изменена'
                            );
                        } else {
                            $this->get('session')->getFlashBag()->add(
                                'danger',
                                'Ошибка при редактировании'
                            );
                        }
                        return $this->redirect($this->generateUrl('vilka_control_subway'));
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
     * Delete Subway entity.
     *
     * @Route("/delete/{id}", name="vilka_control_subway_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        $id = (int)$id;
        if ($id) {
            $subwayRepository = $this->getSubwayRepository();
            $subway = $subwayRepository->find($id);
            if ($subway) {
                $subwayManager = $this->getSubwayManager();
                if ($subwayManager->delete($subway)) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        'Станция метро успешно удалена'
                    );
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
        return $this->redirect($this->generateUrl('vilka_control_subway'));
    }

    protected function entityNotFound() {
        $this->get('session')->getFlashBag()->add(
            'warning',
            'Станция метро не найдена'
        );
        return $this->redirect($this->generateUrl('vilka_control_subway'));
    }
}
