<?php

namespace Ewave\CoreBundle\Controller;

use Ewave\CoreBundle\Manager\EnvironmentManager;
use Ewave\CoreBundle\Manager\HtaManager;
use Ewave\CoreBundle\Manager\MysqlManager;
use Ewave\CoreBundle\Manager\OfficeManager;
use Ewave\CoreBundle\Manager\ProjectManager;
use Ewave\CoreBundle\Manager\SshManager;
use Ewave\CoreBundle\Manager\TeamManager;
use Ewave\CoreBundle\Repository\EnvironmentRepository;
use Ewave\CoreBundle\Repository\HtaRepository;
use Ewave\CoreBundle\Repository\MysqlRepository;
use Ewave\CoreBundle\Repository\OfficeRepository;
use Ewave\CoreBundle\Repository\ProjectRepository;
use Ewave\CoreBundle\Repository\SshRepository;
use Ewave\CoreBundle\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Ewave\CoreBundle\Manager\HistoryManager;
use Ewave\CoreBundle\Manager\SettingManager;
use Ewave\CoreBundle\Manager\UserManager;
use Ewave\CoreBundle\Repository\HistoryRepository;
use Ewave\CoreBundle\Repository\SettingRepository;
use Ewave\CoreBundle\Repository\UserRepository;

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
    private function getRepository($entity, $prefix = 'EwaveCoreBundle')
    {
        return $this->getDoctrine()->getRepository($prefix.':'.$entity);
    }

    /**
     * @return UserManager
     */
    public function getUserManager()
    {
        return $this->get('ewave.manager.user');
    }

    /**
     * @return UserRepository
     */
    public function getUserRepository()
    {
        return $this->getRepository('User');
    }

    /**
     * @return SettingRepository
     */
    public function getSettingRepository()
    {
        return $this->getRepository('Setting');
    }

    /**
     * @return SettingManager
     */
    public function getSettingManager()
    {
        return $this->get('ewave.manager.setting');
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
        return $this->get('ewave.manager.history');
    }

    /**
     * @return TeamRepository
     */
    public function getTeamRepository()
    {
        return $this->getRepository('Team');
    }

    /**
     * @return TeamManager
     */
    public function getTeamManager()
    {
        return $this->get('ewave.manager.team');
    }

    /**
     * @return ProjectRepository
     */
    public function getProjectRepository()
    {
        return $this->getRepository('Project');
    }

    /**
     * @return ProjectManager
     */
    public function getProjectManager()
    {
        return $this->get('ewave.manager.project');
    }

    /**
     * @return EnvironmentRepository
     */
    public function getEnvironmentRepository()
    {
        return $this->getRepository('Environment');
    }

    /**
     * @return EnvironmentManager
     */
    public function getEnvironmentManager()
    {
        return $this->get('ewave.manager.environment');
    }

    /**
     * @return SshRepository
     */
    public function getSshRepository()
    {
        return $this->getRepository('Ssh');
    }

    /**
     * @return SshManager
     */
    public function getSshManager()
    {
        return $this->get('ewave.manager.ssh');
    }

    /**
     * @return MysqlRepository
     */
    public function getMysqlRepository()
    {
        return $this->getRepository('Mysql');
    }

    /**
     * @return MysqlManager
     */
    public function getMysqlManager()
    {
        return $this->get('ewave.manager.mysql');
    }

    /**
     * @return OfficeRepository
     */
    public function getOfficeRepository()
    {
        return $this->getRepository('Office');
    }

    /**
     * @return OfficeManager
     */
    public function getOfficeManager()
    {
        return $this->get('ewave.manager.office');
    }

    /**
     * @return HtaRepository
     */
    public function getHtaRepository()
    {
        return $this->getRepository('Hta');
    }

    /**
     * @return HtaManager
     */
    public function getHtaManager()
    {
        return $this->get('ewave.manager.hta');
    }
}