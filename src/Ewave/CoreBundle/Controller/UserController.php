<?php

namespace Ewave\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Ewave\CoreBundle\Entity\User;
use Ewave\CoreBundle\Form\SearchType;
use Ewave\CoreBundle\Form\UserEditType;
use Ewave\CoreBundle\Form\UserType;

/**
 * User controller.
 *
 * @Route("/control/user")
 */
class UserController extends AdvancedController
{
    /**
     * Lists all User entities.
     *
     * @Route("/{page}", name="ewave_control_user", defaults={"page"=1}, requirements={"page" = "\d+"})
     * @Template("EwaveCoreBundle:User:index.html.twig")
     */
    public function indexAction(Request $request, $page = 1)
    {
        $userRepository = $this->getUserRepository();
        $form = $this->createForm(new SearchType());
        $search = false;
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $search = $data['search'];
            }
        }

        $entities = $userRepository->getList($search, (int)$page);
        $pages = $userRepository->getListPages($search);

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
     * Creates a new User entity.
     *
     * @Route("/create/", name="ewave_control_user_create")
     * @Template("EwaveCoreBundle:User:create.html.twig")
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(new UserType());
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $manager = $this->getUserManager();
                $userRepository = $this->getUserRepository();
                $error = false;
                $user = $userRepository->findOneByUsername($data['username']);
                if ($user) {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'Пользователь с таким именем уже существует'
                    );
                    $error = true;
                }
                $user = $userRepository->findOneByEmail($data['email']);
                if ($user) {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'Пользователь с таким email уже существует'
                    );
                    $error = true;
                }
                if ($data['password'] === $data['password_confirm']) {
                    if (!$error) {
                        if ($manager->create($data)) {
                            $this->get('session')->getFlashBag()->add(
                                'success',
                                'Пользователь успешно создан'
                            );
                            $this->get('session')->set('settings', null);
                        } else {
                            $this->get('session')->getFlashBag()->add(
                                'danger',
                                'Ошибка при создании'
                            );
                        }
                        return $this->redirect($this->generateUrl('ewave_control_user'));
                    }
                } else {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'Пароли не совпадают'
                    );
                }
            }
        }
        return array(
            'form' => $form->createView()
        );
    }

    /**
     * Edit User entity.
     *
     * @Route("/edit/{id}", name="ewave_control_user_edit")
     * @Template("EwaveCoreBundle:User:edit.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $id = (int)$id;
        $form = $this->createForm(new UserEditType());
        if ($id) {
            $userRepository = $this->getUserRepository();
            $user = $userRepository->find($id);
            if ($user) {
                $form->get('username')->setData($user->getUsername());
                $form->get('email')->setData($user->getEmail());
                $form->get('enabled')->setData($user->isEnabled());
                if ($request->getMethod() == 'POST') {
                    $form->handleRequest($request);
                    if ($form->isValid()) {
                        /* @var $data User */
                        $data = $form->getData();
                        $manager = $this->getUserManager();
                        $error = false;
                        $userExist = $userRepository->findOneByUsername($data['username']);
                        if ($userExist && ($user->getId() != $userExist->getId())) {
                            $this->get('session')->getFlashBag()->add(
                                'danger',
                                'Пользователь с таким именем уже существует'
                            );
                            $error = true;
                        }
                        $userExist = $userRepository->findOneByEmail($data['email']);
                        if ($userExist && ($user->getId() != $userExist->getId())) {
                            $this->get('session')->getFlashBag()->add(
                                'danger',
                                'Пользователь с таким email уже существует'
                            );
                            $error = true;
                        }
                        if ($data['password'] === $data['password_confirm']) {
                            if (!$error) {
                                if ($manager->update($data, $user)) {
                                    $this->get('session')->getFlashBag()->add(
                                        'success',
                                        'Пользователь успешно сохранен'
                                    );
                                    $this->get('session')->set('settings', null);
                                } else {
                                    $this->get('session')->getFlashBag()->add(
                                        'danger',
                                        'Ошибка при создании'
                                    );
                                }
                                return $this->redirect($this->generateUrl('ewave_control_user'));
                            }
                        } else {
                            $this->get('session')->getFlashBag()->add(
                                'danger',
                                'Пароли не совпадают'
                            );
                        }
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
     * Delete User entity.
     *
     * @Route("/delete/{id}", name="ewave_control_user_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        $id = (int)$id;
        if ($id) {
            $userRepository = $this->getUserRepository();
            $user = $userRepository->find($id);
            if ($user) {
                $userManager = $this->getUserManager();
                if ($userManager->delete($user)) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        'Пользователь успешно удален'
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
        return $this->redirect($this->generateUrl('ewave_control_user'));
    }

    protected function entityNotFound() {
        $this->get('session')->getFlashBag()->add(
            'warning',
            'Пользователь не найден'
        );
        return $this->redirect($this->generateUrl('ewave_control_user'));
    }
}
