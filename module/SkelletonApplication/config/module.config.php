<?php
namespace SkelletonApplication;

use XelaxAdmin\Router\ListRoute;
use BjyAuthorize\Provider;
use BjyAuthorize\Guard;

$xelaxConfig = array(
	'list_controller' => array(
		'userprofile' => array(
			'name' => 'UserProfile', 
			'controller_class' => 'XelaxAdmin\Controller\ListController', 
			'base_namespace' => 'SkelletonApplication',
			'list_columns' => array('Id' => 'userId', 'Name' => 'displayName'),
			'list_title' => 'User Profiles',
			'create_route' => array(
				'disabled' => true
			),
			'delete_route' => array(
				'disabled' => true
			),
			'route_base' => 'zfcadmin/userprofile',
			'rest_enabled' => true,
			'id_name' => 'userId',
		),
	),
);

$routerConfig = array(
	'home' => array(
		'type' => 'literal',
		'options' => array(
			'route' => '/',
			'defaults' => array(
				'controller' => 'SkelletonApplication\Controller\Index',
				'action'     => 'index',
			),
		),
	),
	'zfcadmin' => array(
		'child_routes' => array(
			'userprofile' => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'userprofile' ) ),
		),
	),
);

$guardConfig = array(
	['route' => 'zfcuser',                  'roles' => ['guest', 'user'] ],
	['route' => 'zfcuser/login',            'roles' => ['guest', 'user'] ],
	['route' => 'zfcuser/register',         'roles' => ['guest'] ],
	['route' => 'zfcuser/authenticate',     'roles' => ['guest'] ],
	['route' => 'zfcuser/logout',           'roles' => ['guest', 'user'] ],
	['route' => 'zfcuser/changepassword',   'roles' => ['user'] ],
	['route' => 'zfcuser/changeemail',      'roles' => ['user'] ],
	['route' => 'home',                     'roles' => ['guest', 'user'] ],

	// modules
	['route' => 'doctrine_orm_module_yuml', 'roles' => ['administrator'] ],

	// admin
	['route' => 'zfcadmin',                      'roles' => ['moderator']],

	// user admin
	['route' => 'zfcadmin/zfcuseradmin',         'roles' => ['administrator']],
	['route' => 'zfcadmin/zfcuseradmin/list',    'roles' => ['administrator']],
	['route' => 'zfcadmin/zfcuseradmin/create',  'roles' => ['administrator']],
	['route' => 'zfcadmin/zfcuseradmin/edit',    'roles' => ['administrator']],
	['route' => 'zfcadmin/zfcuseradmin/remove',  'roles' => ['administrator']],
	['route' => 'zfcadmin/userprofile',          'roles' => ['administrator']],
);

return array(
	'controllers' => array(
		'invokables' => array(
			'SkelletonApplication\Controller\Index' => Controller\IndexController::class,
		),
	),
	
    'xelax' => $xelaxConfig,
	
	'router' => array(
		'routes' => $routerConfig,
	),
	
	'bjyauthorize' => array(
		// resource providers provide a list of resources that will be tracked
        // in the ACL. like roles, they can be hierarchical
        'resource_providers' => array(
            Provider\Resource\Config::class => array(
                'user' => array(),
				'debug' => array(),
            ),
        ),

		
		'rule_providers' => array(
			Provider\Rule\Config::class => array(
                'allow' => array(
					// config for navigation
                    [['user'],  'user', 'profile'],
                    [['user'],  'user', 'logout'],
                    [['user'],  'user', 'changepassword'],
                    [['guest'], 'user', 'login'],
                    [['guest'], 'user', 'register'],
					
					[['moderator'], 'debug', 'moderator'],
					[['administrator'], 'debug', 'administrator'],
                ),

                // Don't mix allow/deny rules if you are using role inheritance.
                // There are some weird bugs.
                'deny' => array(
                    // ...
                ),
            )
		),
		
        'guards' => array(
            Guard\Route::class => $guardConfig
		),
	),
	
	'skelleton_application' => array(
		'roles' => array(
			'guest' => array(),
			'user' => array(
				'moderator' => array(
					'administrator' => array() // Admin role must be leaf and must contain 'admin'
				)
			)
		)
	),
	
	
	
	'service_manager' => array(
		'abstract_factories' => array(
			\Zend\Cache\Service\StorageCacheAbstractServiceFactory::class,
			\Zend\Log\LoggerAbstractServiceFactory::class,
		),
		'invokables' => array(
			'SkelletonApplication\UserListener' => Listener\UserListener::class,
		),
		'factories' => array(
			'Navigation' => \Zend\Navigation\Service\DefaultNavigationFactory::class,
			'SkelletionApplication\Options\Application' => function (\Zend\ServiceManager\ServiceManager $sm) {
				$config = $sm->get('Config');
				return new Options\SkelletonOptions(isset($config['skelleton_application']) ? $config['skelleton_application'] : array());
			},
		),
		'aliases' => array(
			'translator' => 'MvcTranslator',
		),
	),

	// language options
	'translator' => array(
		'locale' => 'de_DE',
		'translation_file_patterns' => array(
			array(
				'type'     => 'gettext',
				'base_dir' => __DIR__ . '/../language',
				'pattern'  => '%s.mo',
			),
		),
	),

	// view options
	'view_manager' => array(
		'display_not_found_reason' => true,
		'display_exceptions'       => true,
		'doctype'                  => 'HTML5',
		'not_found_template'       => 'error/404',
		'exception_template'       => 'error/index',
		'template_map' => array(
			'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
			'error/404'               => __DIR__ . '/../view/error/404.phtml',
			'error/index'             => __DIR__ . '/../view/error/index.phtml',
		),
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),

	// Site navigation
	'navigation' => array(
		// default navigation
		'default' => array(
			array('label' => 'Home',            'route' => 'home'),
			array('label' => 'Admin',           'route' => 'zfcadmin',               'resource' => 'user', 'privilege' => 'login'),
			array('label' => 'Login',           'route' => 'zfcuser/login',          'resource' => 'user', 'privilege' => 'login'),
			array('label' => 'Registrieren',    'route' => 'zfcuser/register',       'resource' => 'user', 'privilege' => 'register'),
			array('label' => 'Profil',          'route' => 'zfcuser',                'resource' => 'user', 'privilege' => 'profile'),
			array('label' => 'Passwort Ändern', 'route' => 'zfcuser/changepassword', 'resource' => 'user', 'privilege' => 'changepassword'),
			array('label' => 'Logout',          'route' => 'zfcuser/logout',         'resource' => 'user', 'privilege' => 'logout'),
		),
		// admin navigation
		'admin' => array(
			array('label' => 'User Profiles',      'route' => 'zfcadmin/userprofile')
		),
	),


	// Placeholder for console routes
	'console' => array(
		'router' => array(
			'routes' => array(
			),
		),
	),

	// doctrine config
	'doctrine' => array(
		'driver' => array(
			__NAMESPACE__ . '_driver' => array(
				'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class, // use AnnotationDriver
				'cache' => 'array',
				'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity') // entity path
			),
			'orm_default' => array(
				'drivers' => array(
					__NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
				)
			)
		),

		// Fixtures to create admin user and default roles
		'fixture' => array(
			'SkelletonApplication_fixture' => __DIR__ . '/../data/Fixtures',
		)
	),
);
