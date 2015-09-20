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
			'list_columns' => array(gettext_noop('Id') => 'userId', gettext_noop('Name') => 'displayName'),
			'list_title' => gettext_noop('User Profiles'),
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
		'user' => array(
			'name' => gettext_noop('User'),
			'controller_class' => 'SkelletonApplication\Controller\User', 
			'base_namespace' => 'SkelletonApplication',
			'list_columns' => array(gettext_noop('Id') => 'id', gettext_noop('Name') => 'display_name', gettext_noop('E-Mail') => 'email', gettext_noop('State') => 'state'),
			'list_title' => gettext_noop('Users'),
			'route_base' => 'zfcadmin/user',
			'rest_enabled' => true,
		),
	),
);

$routerConfig = array(
	'home' => array(
		'type' => 'Segment',
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
			'user'        => array( 'type' => ListRoute::class, 'priority' => 1001, 'options' => array( 'controller_options_name' => 'user'        ) ),
		),
	),
	'zfcuser' => array(
		'child_routes' => array(
			'check-token' => array(
				'type' => 'segment',
				'options' => array(
					'route' => '/activate/:token',
					'defaults' => array(
						'controller' => 'SkelletonApplication\Controller\FrontendUser',
						'action' => 'checkToken',
					),
					'constraints' => array(
						'token' => '[A-F0-9]',
					),
				),
			),
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
	['route' => 'zfcuser/forgotpassword',   'roles' => ['guest']],
	['route' => 'zfcuser/resetpassword',    'roles' => ['guest']],
	
	['route' => 'home',                     'roles' => ['guest', 'user'] ],

	// modules
	['route' => 'doctrine_orm_module_yuml', 'roles' => ['administrator'] ],

	// admin
	['route' => 'zfcadmin',                      'roles' => ['moderator']],

	// user admin
	['route' => 'zfcadmin/userprofile',          'roles' => ['administrator']],
	['route' => 'zfcadmin/user' ,                'roles' => ['administrator']],
);

$ressources = array(
	'debug',
	'user', // navigation ZfcUser
	'administration', // navigation for administration
);

$ressourceAllowRules = array(
	[['user'],  'user', 'profile'],
	[['user'],  'user', 'logout'],
	[['user'],  'user', 'changepassword'],
	[['guest'], 'user', 'login'],
	[['guest'], 'user', 'register'],

	[['moderator'], 'administration', 'login'],
	[['moderator'], 'administration', 'user/list'],
	[['moderator'], 'administration', 'user/create'],
	[['moderator'], 'administration', 'userprofile'],

	[['moderator'], 'debug', 'moderator'],
	[['administrator'], 'debug', 'administrator'],
);

return array(
	'controllers' => array(
		'invokables' => array(
			'SkelletonApplication\Controller\Index' => Controller\IndexController::class,
			'SkelletonApplication\Controller\User' => Controller\UserController::class,
			'SkelletonApplication\Controller\FrontendUser' => Controller\FrontendUserController::class,
		),
	),
	
    'xelax' => $xelaxConfig,
	
	'router' => array(
		'router_class' => 'SkelletonApplication\Mvc\Router\Http\LanguageTreeRouteStack',
		'routes' => $routerConfig,
	),
	
	'bjyauthorize' => array(
		// resource providers provide a list of resources that will be tracked
        // in the ACL. like roles, they can be hierarchical
        'resource_providers' => array(
            Provider\Resource\Config::class => $ressources,
        ),

		
		'rule_providers' => array(
			Provider\Rule\Config::class => array(
                'allow' => $ressourceAllowRules,

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
			'SkelletonApplication\UserService' => Service\UserService::class,
		),
		'factories' => array(
			'Navigation' => \Zend\Navigation\Service\DefaultNavigationFactory::class,
			'SkelletionApplication\Options\Application' => function (\Zend\ServiceManager\ServiceManager $sm) {
				$config = $sm->get('Config');
				return new Options\SkelletonOptions(isset($config['skelleton_application']) ? $config['skelleton_application'] : array());
			},
			'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
			'goaliomailservice_options' => 'SkelletonApplication\Options\Service\TransportOptionsFactory',
			'SkelletonApplication\Options\Site\Email' => 'SkelletonApplication\Options\Service\SiteEmailOptionsFactory',
		),
	),

	// language options
	'translator' => array(
		'locale' => array('de_DE', 'de_DE'),
		'translation_file_patterns' => array(
			array(
				'type'     => 'gettext',
				'base_dir' => __DIR__ . '/../language',
				'pattern'  => '%s.mo',
			),
			array(
				'type'     => 'gettext',
				'base_dir' => __DIR__ . '/../../../vendor/zf-commons/zfc-user/src/ZfcUser/language',
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
			'zfc-user/user/login'     => __DIR__ . '/../view/zfc-user/user/login.phtml',
		),
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),

	'view_helpers' => array(
		'invokables' => array(
			'languageSwitch'          => 'SkelletonApplication\View\Helper\LanguageSwitch',
		),
	),
	
	// Site navigation
	'navigation' => array(
		// default navigation
		'default' => array(
			array('label' => gettext_noop('Home'),            'route' => 'home'),
			array('label' => gettext_noop('Admin'),           'route' => 'zfcadmin',               'resource' => 'administration', 'privilege' => 'login'),
			array('label' => gettext_noop('Login'),           'route' => 'zfcuser/login',          'resource' => 'user', 'privilege' => 'login'),
			array('label' => gettext_noop('Register'),        'route' => 'zfcuser/register',       'resource' => 'user', 'privilege' => 'register'),
			array('label' => gettext_noop('Profile'),         'route' => 'zfcuser',                'resource' => 'user', 'privilege' => 'profile'),
			array('label' => gettext_noop('Change Password'), 'route' => 'zfcuser/changepassword', 'resource' => 'user', 'privilege' => 'changepassword'),
			array('label' => gettext_noop('Logout'),          'route' => 'zfcuser/logout',         'resource' => 'user', 'privilege' => 'logout'),
		),
		// admin navigation
		'admin' => array(
			'zfcuseradmin' => null,
			array('label' => gettext_noop('Home'),            'route' => 'home'),
			array('label' => gettext_noop('Users'),           'route' => 'zfcadmin/user',        'resource' => 'administration', 'privilege' => 'user/list' ),
			array('label' => gettext_noop('User Profiles'),   'route' => 'zfcadmin/userprofile', 'resource' => 'administration', 'privilege' => 'userprofile')
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
