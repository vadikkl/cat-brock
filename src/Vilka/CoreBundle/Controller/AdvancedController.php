<?php

namespace Vilka\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Vilka\CoreBundle\Manager\RestaurantManager;
use Vilka\CoreBundle\Manager\SubwayManager;
use Vilka\CoreBundle\Manager\UserManager;
use Vilka\CoreBundle\Repository\SettingRepository;
use Vilka\CoreBundle\Repository\UserRepository;
use Vilka\CoreBundle\Repository\RestaurantRepository;
use Vilka\CoreBundle\Repository\CompanyRepository;
use Vilka\CoreBundle\Repository\CityRepository;
use Vilka\CoreBundle\Repository\DistrictRepository;
use Vilka\CoreBundle\Repository\SubwayRepository;
use Vilka\CoreBundle\Repository\KitchenRepository;
use Vilka\CoreBundle\Repository\CardRepository;
use Vilka\CoreBundle\Repository\ClassificationRepository;
use Vilka\CoreBundle\Repository\EntertainmentRepository;
use Vilka\CoreBundle\Repository\EventRepository;
use Vilka\CoreBundle\Repository\MenuFeatureRepository;
use Vilka\CoreBundle\Repository\FeatureRepository;
use Vilka\CoreBundle\Repository\MusicRepository;

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
     * @return SubwayManager
     */
    public function getSubwayManager()
    {
        return $this->get('vilka.manager.subway');
    }

    /**
     * @return UserRepository
     */
    public function getUserRepository()
    {
        return $this->getRepository('User');
    }

    /**
     * @return CityRepository
     */
    public function getCityRepository()
    {
        return $this->getRepository('City');
    }

    /**
     * @return CompanyRepository
     */
    public function getCompanyRepository()
    {
        return $this->getRepository('Company');
    }

    /**
     * @return SettingRepository
     */
    public function getSettingRepository()
    {
        return $this->getRepository('Setting');
    }

    /**
     * @return DistrictRepository
     */
    public function getDistrictRepository()
    {
        return $this->getRepository('District');
    }

    /**
     * @return SubwayRepository
     */
    public function getSubwayRepository()
    {
        return $this->getRepository('Subway');
    }

    /**
     * @return KitchenRepository
     */
    public function getKitchenRepository()
    {
        return $this->getRepository('Kitchen');
    }

    /**
     * @return CardRepository
     */
    public function getCardRepository()
    {
        return $this->getRepository('Card');
    }

    /**
     * @return ClassificationRepository
     */
    public function getClassificationRepository()
    {
        return $this->getRepository('Classification');
    }

    /**
     * @return EntertainmentRepository
     */
    public function getEntertainmentRepository()
    {
        return $this->getRepository('Entertainment');
    }

    /**
     * @return EventRepository
     */
    public function getEventRepository()
    {
        return $this->getRepository('Event');
    }

    /**
     * @return MenuFeatureRepository
     */
    public function getMenuFeatureRepository()
    {
        return $this->getRepository('MenuFeature');
    }

    /**
     * @return FeatureRepository
     */
    public function getFeatureRepository()
    {
        return $this->getRepository('Feature');
    }

    /**
     * @return MusicRepository
     */
    public function getMusicRepository()
    {
        return $this->getRepository('Music');
    }

    /**
     * @return RestaurantManager
     */
    public function getRestaurantManager()
    {
        return $this->get('vilka.manager.restaurant');
    }

    /**
     * @return RestaurantRepository
     */
    public function getRestaurantRepository()
    {
        return $this->getRepository('Restaurant');
    }
}