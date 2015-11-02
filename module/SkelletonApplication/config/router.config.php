<?php
namespace SkelletonApplication;

use XelaxAdmin\Router\ListRoute;

array(
	'home' => array(
		'type' => 'Segment',
		'options' => array(
			'route' => '/',
			'defaults' => array(
				'controller' => Controller\IndexController::class,
				'action'     => 'index',
			),
		),
	),
	'zfcadmin' => array(
		'child_routes' => array(
			'userprofile' => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'userprofile' ) ),
			'roles'       => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'role' ) ),
			'user'        => array( 'type' => ListRoute::class, 'priority' => 1001, 'options' => array( 'controller_options_name' => 'user'        ) ,
				'may_terminate' => true,
				'child_routes' => array(
					'block'   => array( 
						'type' => 'segment', 
						'options' => array(
							'route' => '/block/:userId', 
							'constraints' => array(
								'userId' => '[0-9]+',
							),
							'defaults' => array(
								'controller' => Controller\UserController::class, 
								'action' => 'block',
							),
						), 
					),
					'unblock'   => array( 
						'type' => 'segment', 
						'options' => array(
							'route' => '/unblock/:userId', 
							'constraints' => array(
								'userId' => '[0-9]+',
							),
							'defaults' => array(
								'controller' => Controller\UserController::class, 
								'action' => 'unblock',
							),
						), 
					),
				),
			),
			'siteconfig'      => array( 
				'child_routes' => array(
					'registration' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/registration[/:action]',
							'defaults' => array(
								'controller' => Controller\RegistrationConfigController::class,
								'action' => 'index',
							),
							'constraints' => array(
								'action' => '(index|edit)',
							),
						),
					),
					'emails' => array(
						'type' => 'segment',
						'options' => array(
							'route' => '/emails[/:action]',
							'defaults' => array(
								'controller' => Controller\EmailTemplateController::class,
								'action' => 'index',
							),
							'constraints' => array(
								'action' => '(index|edit)',
							),
						),
					),
				)
			),
		),
	),
	'zfcuser' => array(
		'child_routes' => array(
			'check-token' => array(
				'type' => 'segment',
				'options' => array(
					'route' => '/activate/:token',
					'defaults' => array(
						'controller' => Controller\FrontendUserController::class,
						'action' => 'checkToken',
					),
					'constraints' => array(
						'token' => '[A-F0-9]+',
					),
				),
			),
			'register' => array(
				'may_terminate' => true,
				'options' => array(
					'defaults' => array(
						'controller' => Controller\FrontendUserController::class,
					),
				),
				'child_routes' => array(
					'registered' => array(
						'type' => 'literal',
						'options' => array(
							'route' => '/finished',
							'defaults' => array(
								'controller' => Controller\FrontendUserController::class,
								'action' => 'registered',
							),
						),
					),
				),
			),
		),
	),
);