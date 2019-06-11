<?php
/**
 * GENEREL CONFIGURATION
 * top level of your configuration
 */

return array(
    /* database information */
    'db' => array(
        'driver' => 'Pdo',
        'dsn' => 'pgsql:host=localhost;port=5432;dbname=fluxtime;user=postgres;password=root',
    ),
    
    /* Service manager */
    'service_manager' => array(
        
        // an array of service name/factory class name pairs.
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
            'Navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ),
        
        // nicklas please comment
        'service_manager' => array(
            // alias name/target name pairs (where the target name may also be an alias).
            'aliases' => array(
                'translator' => 'MvcTranslator',
            ),
        ),
    ),
    
    /* Navigation - pages of */
    'navigation' => array(
        'default' => array(
            'dansk' => array(
                'buttonname' => 'DANSK',
                'route' => 'profile',
                'action' => 'changeLanguage',
                'class' => 'flag-icon flag-icon-dk fa flux-flag',
            ),
            'engelsk' => array(
                'buttonname' => 'ENGLISH',
                'route' => 'profile',
                'action' => 'changeLanguage',
                'class' => 'flag-icon flag-icon-gb fa flux-flag',
            ),
            'logout' => array(
                'buttonname' => 'LOGOUT',
                'route' => 'logout',
                'action' => 'logout',
                'class' => 'fa fa-sign-out fa-3x',
            ),
            'fluxuser' => array(
                'buttonname' => 'USERS',
                'route' => 'fluxuser',
                'action' => 'index',
                'class' => 'fa fa-users fa-3x',
            ),
            'invoice' => array(
                'buttonname' => 'CLIENTS',
                'route' => 'client',
                'action' => 'index',
                'class' => 'fa fa-briefcase fa-3x',
            ),
           'report' => array(
                'buttonname' => 'REPORTS',
                'route' => 'report',
               'action' => 'index',
                'class' => 'fa fa-copy fa-3x',
            ),
           
             'log' => array(
                'buttonname' => 'LOG',
                'route' => 'timereg',
                 'action' => 'index',
                'class' => 'fa fa-book fa-3x',
            ),
              'project' => array(
                'buttonname' => 'PROJECTS',
                'route' => 'project',
                   'action' => 'index',
                'class' => 'fa fa-folder-open fa-3x',
            ),
             'timelog' => array(
                'buttonname' => 'MY LOG',
                'route' => 'timereg',
                'action' => 'mytimereg',
                'class' => 'fa fa-list fa-3x',
            ),
              'mytask' => array(
                'buttonname' => 'MY TASKS',
                'route' => 'mytask',
                'action' => 'mytasks',
                'class' => 'fa fa-tasks fa-3x',
            ),
              'profile' => array(
                'buttonname' => 'PROFILE',
                'route' => 'profile',
                'action' => 'edit',
                'class' => 'fa fa-user fa-3x',
            )
        ),
    ),
);
