<?php

namespace Fluxuser;

use Doctrine\DBAL\Types\Type;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class Module {

    // module manager uses autoloading configuration
    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    'ActionHelper' => __DIR__ . '/src/Fluxuser/Utils',
                ),
            )
        );
    }

    // module manager configuration    
    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    // Zend\ModuleManager\Feature\ServiceProvider will call´getServiceConfig an merge 
    public function getServiceConfig() {
        return array(
            'factories' => array(
                'Zend\Authentication\AuthenticationService' => function($serviceManager) {
                    return $serviceManager->get('doctrine.authenticationservice.orm_default');
                },
            ),
        );
    }
    
    /**
     * Xtra view helpers
     * @return type
     */
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'encrypt' => function($sm) {
                    $helper = new \Fluxuser\View\Helper\EncryptionHelper($name);
                    return $helper;
                }
            )
        );   
   }    

    // booting up   
    public function onBootstrap(MvcEvent $e) {
        // special datetime
        if (!Type::hasType('UTCDateTime')) {
            Type::addType('UTCDateTime', 'Fluxuser\Types\UTCDateTimeType');
        }

        // on booting load the permissions
        // added for Acl - user permissions
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach('route', array($this, 'loadConfiguration'), 2);

        // to translate 
        date_default_timezone_set('Europe/Madrid');

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $serviceManager = $e->getApplication()->getServiceManager();
        $translator = $serviceManager->get('translator');
        $this->initTranslator($e);
    }

    /**
     * Initialize translator 
     * @param MvcEvent $event
     */
    protected function initTranslator(MvcEvent $event) {
        $serviceManager = $event->getApplication()->getServiceManager();

        // Zend\Session\Container
        $session = New Container('language');
        $session->offsetSet('English', 'en_US');
        $session->offsetSet('Danish', 'da_DK');

        $translator = $serviceManager->get('translator');
        if ($session->offsetGet('Current') === NULL) {
            $translator->setLocale($session->offsetGet('English'));
        } else {
            $translator->setLocale($session->offsetGet('Current'));
        }
    }

    // added for Acl - user permissions
    public function loadConfiguration(MvcEvent $e) {
        $application = $e->getApplication();
        $sm = $application->getServiceManager();
        $sharedManager = $application->getEventManager()->getSharedManager();
        $router = $sm->get('router');
        $request = $sm->get('request');
        $matchedRoute = $router->match($request);
        // use access plug-in 
        if (null !== $matchedRoute) {
            $sharedManager->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) use ($sm) {
                $sm->get('ControllerPluginManager')->get('FlexuserPlugin')->doAuthorization($e); //pass to the plugin..
            }, 2
            );
        }
    }

}
