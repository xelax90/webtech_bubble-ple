<?php
namespace SkelletonApplication;

use SkelletonApplication\Options;

return array(
	'controllers' => array(
		'invokables' => array(
			'SkelletonApplication\Controller\Index' => 'SkelletonApplication\Controller\IndexController',
		),
	),
	
	// Routes
	'router' => array(
		'routes' => array(
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
		),
	),
	
	'bjyauthorize' => array(
		// resource providers provide a list of resources that will be tracked
        // in the ACL. like roles, they can be hierarchical
        'resource_providers' => array(
            "BjyAuthorize\Provider\Resource\Config" => array(
                'user' => array(),
            ),
        ),

		
		'rule_providers' => array(
			"BjyAuthorize\Provider\Rule\Config" => array(
                'allow' => array(
					// config for navigation
                    [['user'], 'user', 'profile'],
                    [['user'], 'user', 'logout'],
                    [['user'], 'user', 'changepassword'],
                    [['guest'], 'user', 'login'],
                    [['guest'], 'user', 'register'],
                ),

                // Don't mix allow/deny rules if you are using role inheritance.
                // There are some weird bugs.
                'deny' => array(
                    // ...
                ),
            )
		),
		
		'guards' => array(
			'BjyAuthorize\Guard\Controller' => array(
				[ // ZfcUser public
					'controller' => 'zfcuser', 
					'action' => ['login', 'register', 'authenticate'], 
					'roles' => ['guest']
				],
				[ // ZfcUser private
					'controller' => 'zfcuser', 
					'action' => ['logout', 'index', 'changepassword', 'changeEmail'], 
					'roles' => ['user']
				],
				// home
				['controller' => 'SkelletonApplication\Controller\Index', 'roles' => ['guest', 'user']],
				// Yuml diagram
				['controller' => 'DoctrineORMModule\Yuml\YumlController', 'roles' => ['administrator']],
			),
		)
		
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
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
		'factories' => array(
			'Navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
			'SkelletionApplication\Options\Application' => function ($sm) {
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
			array('label' => 'Login',           'route' => 'zfcuser/login',          'resource' => 'user', 'privilege' => 'login'),
			array('label' => 'Registrieren',    'route' => 'zfcuser/register',       'resource' => 'user', 'privilege' => 'register'),
			array('label' => 'Profil',          'route' => 'zfcuser',                'resource' => 'user', 'privilege' => 'profile'),
			array('label' => 'Passwort Ändern', 'route' => 'zfcuser/changepassword', 'resource' => 'user', 'privilege' => 'changepassword'),
			array('label' => 'Logout',          'route' => 'zfcuser/logout',         'resource' => 'user', 'privilege' => 'logout'),
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
				'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver', // use AnnotationDriver
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
