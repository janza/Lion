<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Lion',
    // preloading 'log' component
    'preload' => array('log'),
    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.extensions.sftp.*',
        'application.extensions.restfullyii.components.*',
	'application.extensions.yii-mail.YiiMailMessage',
    ),
    'modules' => array(
        // uncomment the following to enable the Gii tool

        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => 'lpostruzin',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array('127.0.0.1', '161.53.67.202', '::1'),
        ),
    ),
    // application components
    'components' => array(


    // Default properties for some widgets
    'widgetFactory' => array(
        'widgets' => array(
            'CGridView' => array(
                'itemsCssClass' => 'table table-striped table-hover'
            ),
            'CLinkPager' => array(
              'cssFile' => false
            ),
            'CMenu' => array(
              'itemCssClass' => 'nav',
            ),
            'CDetailView' => array(
              'htmlOptions' => array(
                'class' => 'table details-view'
              ),
            )
        ),
    ),
	'mail' => array(
 			'class' => 'ext.yii-mail.YiiMail',
 			'transportType' => 'php',
 			'viewPath' => 'application.views.mail',
 			'logging' => true,
 			'dryRun' => false
 		),
        'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true,
        ),
        'ftp' => array(
            'class' => 'application.extensions.ftp.EFtpComponent',
            'host' => '127.0.0.1',
            'port' => 21,
            'username' => 'lpostruzin',
            'password' => 'lpostruzin',
            'ssl' => false,
            'timeout' => 90,
            'autoConnect' => true,
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'rules' => array(
                // REST patterns
                'api/<controller:\w+>' => array('<controller>/restList', 'verb' => 'GET'),
                'api/<controller:\w+>/<id:\w+>' => array('<controller>/restView', 'verb' => 'GET'),
                'api/<controller:\w+>/<id:\w+>/<var:\w+>' => array('<controller>/restView', 'verb' => 'GET'),
                array('<controller>/restUpdate', 'pattern' => 'api/<controller:\w+>/<id:\d+>', 'verb' => 'PUT'),
                array('<controller>/restDelete', 'pattern' => 'api/<controller:\w+>/<id:\d+>', 'verb' => 'DELETE'),
                array('<controller>/restCreate', 'pattern' => 'api/<controller:\w+>', 'verb' => 'POST'),
                array('<controller>/restCreate', 'pattern' => 'api/<controller:\w+>/<id:\w+>', 'verb' => 'POST'),
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
        ),
        // uncomment the following to enable URLs in path-format
        /*
          'urlManager'=>array(
          'urlFormat'=>'path',
          'rules'=>array(
          '<controller:\w+>/<id:\d+>'=>'<controller>/view',
          '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
          '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
          ),
          ),
         */

        /*
          'db'=>array(
          'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
          ),
         */

        // uncomment the following to use a MySQL database
        /*
          'db'=>array(
          'connectionString' => 'mysql:host=localhost;dbname=testdrive',
          'emulatePrepare' => true,
          'username' => 'root',
          'password' => '',
          'charset' => 'utf8',
          ),
         */

        'db' => array(
            'connectionString' => 'pgsql:host=localhost;port=5432;dbname=coldwatchDB',
            'username' => 'coldwatch',
            'password' => 'coldwatch',
        ),
        /*
          'db'=>array(
          'connectionString'=>'pgsql:host=localhost;port=5432;dbname=coldwatchDB;username=coldwatch;password=coldwatch',
          ),
         */
        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => 'site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
            // uncomment the following to show log messages on web pages
            /*
              array(
              'class'=>'CWebLogRoute',
              ),
             */
            ),
        ),
    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // this is used in contact page
        'adminEmail' => 'hyracoidea@gmail.com',
    ),
);
