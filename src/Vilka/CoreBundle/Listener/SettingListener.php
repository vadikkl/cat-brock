<?php

namespace Vilka\CoreBundle\Listener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Vilka\CoreBundle\Repository\SettingRepository;

/**
 * @DI\Service("vilka.listener.setting")
 * @DI\Tag("kernel.event_listener", attributes = {
 *      "event" = "kernel.exception",
 *      "method"="onKernelRequest"
 * })
 */
class SettingListener implements EventSubscriberInterface
{
    /**
     * @var Registry
     */
    private $doctrine;

    private $_settings = array('site_name' => 'Parser');

    /**
     * @param Registry $doctrine
     *
     * @DI\InjectParams({
     *     "doctrine" = @DI\Inject("doctrine")
     * })
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    private function getDoctrine()
    {
        return $this->doctrine;
    }

    private function getRepository($entity, $namespace = 'VilkaCoreBundle')
    {
        return $this->getDoctrine()->getRepository($namespace.':'.$entity);
    }

    /**
     * @DI\Observe(KernelEvents::REQUEST)
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        /**
         * @var Request $request
         */
        $request = $event->getRequest();
        $session = $request->getSession();
        if (!$request->hasPreviousSession()) {
            return;
        }

        $settings = $session->get('settings', false);
        if (!$settings) {
            $settingsRepository = $this->getRepository('Setting');
            $settings = $settingsRepository->findAll();
            foreach ($settings as $_setting) {
                $this->_settings[$_setting->getName()] = $_setting->getValue();
            }
            $session->set('settings', $this->_settings);
        }

    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered before the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),//?
        );
    }

    /**
     * @param Session $session
     * @param $locale
     */
    public static function setSessionLocale(Session $session, $locale)
    {
        $session->set('_locale', $locale);
    }
}
