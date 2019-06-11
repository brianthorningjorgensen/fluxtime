<?php

/**
 * Configuration of the fluxusermodule
 */

namespace Fluxuser;

return array(
    /* controlers */
    'controllers' => array(
        'invokables' => array(
            'Fluxuser\Controller\Fluxuser' => 'Fluxuser\Controller\FluxuserController', //Routing
            'Fluxuser\Controller\Project'  => 'Fluxuser\Controller\ProjectController', //Routing
            'Fluxuser\Controller\Task'     => 'Fluxuser\Controller\TaskController', //Routing
            'Fluxuser\Controller\Account'  => 'Fluxuser\Controller\AccountController', //Routing
            'Fluxuser\Controller\Timereg'  => 'Fluxuser\Controller\TimeregController', //Routing
            'Fluxuser\Controller\Login'    => 'Fluxuser\Controller\LoginController', //Routing
            'Fluxuser\Controller\Label'    => 'Fluxuser\Controller\LabelController', //Routing
            'Fluxuser\Controller\Profile'  => 'Fluxuser\Controller\ProfileController', //Routing
            'Fluxuser\Controller\Client'   => 'Fluxuser\Controller\ClientController', //Routing
            'Fluxuser\Controller\Contact'  => 'Fluxuser\Controller\ContactController', //Routing
            'Fluxuser\Controller\Console'  => 'Fluxuser\Controller\ConsoleController', //Routing
            'Fluxuser\Controller\Report' => 'Fluxuser\Controller\ReportController', //Routing
            'Fluxuser\Controller\Mytask' => 'Fluxuser\Controller\MytaskController', //Routing
        ),
    ),
    /* Routes */
    'router' => array(//Routes
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Fluxuser\Controller\Login',
                        'action' => 'login',
                    ),
                ),
            ),
            'login' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/login',
                    'defaults' => array(
                        'controller' => 'Fluxuser\Controller\Login',
                        'action' => 'login',
                    ),
                ),
            ),
            'logout' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/logout',
                    'defaults' => array(
                        'controller' => 'Fluxuser\Controller\Login',
                        'action' => 'logout',
                    ),
                    'may_terminate' => true,
                ),
            ),
            'fluxuser' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/fluxuser[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*', //RegEx url action
                    ),
                    'defaults' => array(
                        'controller' => 'Fluxuser\Controller\Fluxuser',
                        'action' => 'index', //index
                    ),
                    'may_terminate' => true,
                ),
            ),
            'project' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/project[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*', //RegEx url action
                    ),
                    'defaults' => array(
                        'controller' => 'Fluxuser\Controller\Project', //Index
                        'action' => 'index',
                    ),
                    'may_terminate' => true,
                ),
            ),
            'task' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/task[/:action][/:id]',
                    'constraints' => array(
                      'action' => '[a-zA-Z][a-zA-Z0-9_-]*', //RegEx url action
                    ),
                    'defaults' => array(
                        'controller' => 'Fluxuser\Controller\Task', //Index
//                        'action' => 'mytasks',
                    ),
                    'may_terminate' => true,
                ),
            ),
              'mytask' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/mytask[/:action][/:id]',
                    'constraints' => array(
                      'action' => '[a-zA-Z][a-zA-Z0-9_-]*', //RegEx url action
                    ),
                    'defaults' => array(
                        'controller' => 'Fluxuser\Controller\Mytask', //Index
                        'action' => 'mytasks',
                    ),
                    'may_terminate' => true,
                ),
            ),
            'timereg' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/timereg[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*', //RegEx url action
                    ),
                    'defaults' => array(
                        'controller' => 'Fluxuser\Controller\Timereg',
                        'action' => 'index',
                    ),
                    'may_terminate' => true,
                ),
            ),
            'account' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/account[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*', //RegEx url action
                    ),
                    'defaults' => array(
                        'controller' => 'Fluxuser\Controller\Account', //Index
                        'action' => 'index',
                    ),
                    'may_terminate' => true,
                ),
            ),
            'label' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/label[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Fluxuser\Controller\Label',
                    ),
                    'may_terminate' => true,
                ),
            ),
            'profile' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/profile[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Fluxuser\Controller\Profile',
                    ),
                    'may_terminate' => true,
                ),
            ),
              'client' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/client[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Fluxuser\Controller\Client',
                        'action' => 'index',
                    ),
                    'may_terminate' => true,
                ),
            ),
              'contact' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/contact[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Fluxuser\Controller\Contact',
                    ),
                    'may_terminate' => true,
                ),
            ),
              'report' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/report[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Fluxuser\Controller\Report',
                    ),
                    'may_terminate' => true,
                ),
            ),
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'cronjob' => array(
                    'options' => array(
                        'route'    => 'cronjob',
                        'defaults' => array(
                            'controller' => 'Fluxuser\Controller\Console',
                            'action' => 'crontasks'
                        )
                    )
                )
            )
        )
    ),
    /* Service manager config */
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory', // datacaching
            'Zend\Log\LoggerAbstractServiceFactory', // service manager
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    /* Translator */
    'translator' => array(
        'locale' => '',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    /* Path in the view */
    'view_manager' => array(//Stier view
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/Fluxuser/layout/layout.phtml',
            'application/Fluxuser/index' => __DIR__ . '/../view/Fluxuser/Fluxuser/index.phtml',
            'error/404' => __DIR__ . '/../view/Fluxuser/error/404.phtml',
            'error/index' => __DIR__ . '/../view/Fluxuser/error/index.phtml',
            'navipart' => __DIR__ . '/../view/Fluxuser/layout/menu.phtml',
            'download/download-csv' => __DIR__ . '/../view/Fluxuser/download/csvrenderer.phtml',
        ),
        'template_path_stack' => array(
            'Fluxuser' => __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    /* Access controle plugin */
    'controller_plugins' => array(
        'invokables' => array(
            'FlexuserPlugin' => 'Fluxuser\Controller\Plugin\MyAccessPlugin',
        ),
    ),
    /* Persistence of database uses the ORM Dcotrine */
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ),
            ),
        ),
        'authentication' => array(
            'orm_default' => array(
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'Fluxuser\Entity\Fluxuser',
                'identity_property' => 'username',
                'credential_property' => 'password',
            ),
        ),
    ),
);
