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
	
	'skelleton_application' => array(
		'guest_role' => 'guest',
		'user_role' => 'user',
		'moderator_role' => 'moderator',
		'admin_role' => 'administrator',
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
		'default' => array(
			array('label' => 'Home',   'route' => 'home'),
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
