<?php

namespace Vilka\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Vilka\CoreBundle\Manager\HistoryManager;
use Vilka\CoreBundle\Manager\SettingManager;
use Vilka\CoreBundle\Manager\UserManager;
use Vilka\CoreBundle\Repository\CatalogRepository;
use Vilka\CoreBundle\Repository\FileRepository;
use Vilka\CoreBundle\Repository\HistoryRepository;
use Vilka\CoreBundle\Repository\SettingRepository;
use Vilka\CoreBundle\Repository\UserRepository;
use Vilka\CoreBundle\Repository\RestaurantRepository;

class AdvancedController extends Controller
{
    /**
     * @param string $routeName
     * @param array $params
     * @return RedirectResponse
     */
    public function redirectRoute($routeName, $params = array())
    {
        $url = $this->generateUrl($routeName, $params);
        return $this->redirect($url);
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function isAjax(Request $request)
    {
        return $request->isXmlHttpRequest();
    }


    /**
     * @param string $message
     * @return JsonResponse
     */
    public function jsonError($message)
    {
        return new JsonResponse(array('error' => $message));
    }

    /**
     * @param string $routeName
     * @param array $routeParams
     * @return JsonResponse
     */
    public function jsonRedirect($routeName, $routeParams = array())
    {
        return new JsonResponse(array(
            'url' => $this->generateUrl($routeName, $routeParams)
        ));
    }

    /**
     * @param string $url
     * @return JsonResponse
     */
    public function jsonRedirectUrl($url)
    {
        return new JsonResponse(array(
            'url' => $url
        ));
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {

            return true;
        }

        return false;
    }

    /**
     * @return JsonResponse
     */
    public function json()
    {
        $params = func_get_args();
        if (count($params) == 1) {
            $params = $params[0];
        }
        return new JsonResponse($params);
    }

    /**
     * @param string $entity
     * @param string $prefix
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    private function getRepository($entity, $prefix = 'VilkaCoreBundle')
    {
        return $this->getDoctrine()->getRepository($prefix.':'.$entity);
    }

    /**
     * @return UserManager
     */
    public function getUserManager()
    {
        return $this->get('vilka.manager.user');
    }

    /**
     * @return UserRepository
     */
    public function getUserRepository()
    {
        return $this->getRepository('User');
    }

    /**
     * @return CatalogRepository
     */
    public function getCatalogRepository()
    {
        return $this->getRepository('Catalog');
    }

    /**
     * @return SettingRepository
     */
    public function getSettingRepository()
    {
        return $this->getRepository('Setting');
    }

    /**
     * @return FileRepository
     */
    public function getFileRepository()
    {
        return $this->getRepository('File');
    }

    /**
     * @return SettingManager
     */
    public function getSettingManager()
    {
        return $this->get('vilka.manager.setting');
    }

    /**
     * @return HistoryRepository
     */
    public function getHistoryRepository()
    {
        return $this->getRepository('History');
    }

    /**
     * @return HistoryManager
     */
    public function getHistoryManager()
    {
        return $this->get('vilka.manager.history');
    }
}