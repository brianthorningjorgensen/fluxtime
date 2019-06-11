<?php

/**
 * Local Configuration Override
 *
 * This configuration override file is for overriding environment-specific and
 * security-sensitive configuration information. Copy this file without the
 * .dist extension at the end and populate values as needed.
 *
 * @NOTE: This file is ignored from Git by default with the .gitignore included
 * in ZendSkeletonApplication. This is a good practice, as it prevents sensitive
 * credentials from accidentally being committed into version control.
 */
return array(
    // database information
    'db' => array(
        'username' => 'postgres',
        'password' => 'root',
    ),
    // doctrine orm settings
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOPgSql\Driver',
                'params' => array(
                    'host'     => 'localhost',
                    'port'     => '5432',
                    'user'     => 'postgres',
                    'password' => 'root',
                    'dbname'   => 'fluxtime',
                ),
            ),
        ),
        'entitymanager' => array(
            'orm_default' => array(
                'connection'    => 'orm_default',
                'configuration' => 'orm_default'
            ),
        ),
    ),
    'google' => array(
        'id'       => '322076718470-qm99k3frg1kqtvlhurj2vc81194jpgie.apps.googleusercontent.com',
        'secret'   => 'd48PjlXJRl0u4efL2fC2bJMO',
        'redirect' => 'http://localhost',
    )
);
