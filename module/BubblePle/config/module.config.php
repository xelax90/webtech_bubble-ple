<?php

/* 
 * Copyright (C) 2015 schurix
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace BubblePle;

use BjyAuthorize\Provider;
use BjyAuthorize\Guard;
use XelaxAdmin\Router\ListRoute;

$xelaxConfig = array(
	/*
	 * Configure your list controllers. Routes are generated automatically, and
	 * access permissions can be configured.
	 */
	'list_controller' => include __DIR__ .'/xelax.config.php',
);

$routerConfig = array(
	'home' => array(
		'options' => array(
			'defaults' => array(
				'controller' => Controller\IndexController::class,
			)
		),
	),
	'test' => array(
		'type' => 'Literal',
		'options' => array(
			'route' => '/test',
			'defaults' => array(
				'controller' => Controller\BubbleController::class,
				'action'     => 'renderForm',
				'bubbleType' => 'BubblePle\Entity\FileAttachment',
				'bubbleId' => 10,
			),
		),
	),
	
	'zfcadmin' => array(
		'child_routes' => array(
			'bubblePLE' => array(
				'type' => 'Literal',
				'options' => array(
					'route' => '/bubblePLE',
				),
				'may_terminate' => false,
				'child_routes' => array(
					'edges'              => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'edges', )),
					'bubbles'            => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'bubbles', )),
					'semesters'          => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'semesters', )),
					'courses'            => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'courses', )),
					'l2pmaterialfolders' => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'l2pmaterialfolders', )),
					'attachments'        => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'attachments', )),
					'fileAttachments'    => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'fileAttachments', )),
					'l2pmaterialattachments' => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'l2pmaterialattachments', )),
					'mediaAttachments'   => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'mediaAttachments', )),
					'imageAttachments'   => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'imageAttachments', )),
					'videoAttachments'   => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'videoAttachments', )),
					'linkAttachments'    => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'linkAttachments', )),
					'filter' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => '/filter[/parent/:parent]',
							'constraints' => array(
								'parent' => '[0-9]+',
							),
							'defaults' => array(
								'controller' => Controller\BubbleController::class,
								'action' => 'filter',
							),
						)
					),
					'form' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => '/form/:bubbleType[/:bubbleId]',
							'constraints' => array(
								'bubbleId' => '[0-9]*',
								'bubbleType' => "[A-Za-z0-9%]*",
							),
							'defaults' => array(
								'controller' => Controller\BubbleController::class,
								'action' => 'renderForm',
							),
						)
					),
					'sync' => array(
						'type' => 'Literal',
						'options' => array(
							'route' => '/sync',
							'defaults' => array(
								'controller' => Controller\BubbleController::class,
								'action' => 'sync',
							),
						)
					),
					'share' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => '/share/:bubbleId/:userId',
							'constraints' => array(
								'bubbleId' => '[0-9]+',
								'userId' => '[0-9]+',
							),
							'defaults' => array(
								'controller' => Controller\BubbleController::class,
								'action' => 'share',
							),
						)
					),
					'unshare' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => '/unshare/:bubbleId/:userId',
							'constraints' => array(
								'bubbleId' => '[0-9]+',
								'userId' => '[0-9]+',
							),
							'defaults' => array(
								'controller' => Controller\BubbleController::class,
								'action' => 'unShare',
							),
						)
					),
					'usernames' => array(
						'type' => 'Literal',
						'options' => array(
							'route' => '/usernames',
							'defaults' => array(
								'controller' => Controller\BubbleController::class,
								'action' => 'usernames',
							),
						)
					),
					'updatePositions' => array(
						'type' => 'Literal',
						'options' => array(
							'route' => '/updatePositions',
							'defaults' => array(
								'controller' => Controller\BubbleController::class,
								'action' => 'updatePositions',
							),
						)
					),
				)
			),
		)
	),
);

$guardConfig = array(
	'test' => ['route' => 'test', 'roles' => ['guest', 'user'] ],
	['route' => 'zfcadmin/bubblePLE/updatePositions',    'roles' => ['user'] ],
	['route' => 'zfcadmin/bubblePLE/sync',               'roles' => ['user'] ],
	['route' => 'zfcadmin/bubblePLE/filter',             'roles' => ['user'] ],
	['route' => 'zfcadmin/bubblePLE/form',               'roles' => ['user'] ],
	['route' => 'zfcadmin/bubblePLE/usernames',          'roles' => ['user'] ],
	['route' => 'zfcadmin/bubblePLE/share',              'roles' => ['user'] ],
	['route' => 'zfcadmin/bubblePLE/unshare',            'roles' => ['user'] ],
	['route' => 'zfcadmin/bubblePLE/edges',              'roles' => ['user'] ],
	['route' => 'zfcadmin/bubblePLE/bubbles',            'roles' => ['user'] ],
	['route' => 'zfcadmin/bubblePLE/semesters',          'roles' => ['user'] ],
	['route' => 'zfcadmin/bubblePLE/courses',            'roles' => ['user'] ],
	['route' => 'zfcadmin/bubblePLE/l2pmaterialfolders', 'roles' => ['user'] ],
	['route' => 'zfcadmin/bubblePLE/l2pmaterialattachments', 'roles' => ['user'] ],
	['route' => 'zfcadmin/bubblePLE/attachments',        'roles' => ['user'] ],
	['route' => 'zfcadmin/bubblePLE/fileAttachments',    'roles' => ['user'] ],
	['route' => 'zfcadmin/bubblePLE/mediaAttachments',   'roles' => ['user'] ],
	['route' => 'zfcadmin/bubblePLE/imageAttachments',   'roles' => ['user'] ],
	['route' => 'zfcadmin/bubblePLE/videoAttachments',   'roles' => ['user'] ],
	['route' => 'zfcadmin/bubblePLE/linkAttachments',    'roles' => ['user'] ],
);

$ressources = array(
	'bubblePLE',
);

$ressourceAllowRules = array(
	[['moderator'], 'bubblePLE', 'edges/list'],
	[['moderator'], 'bubblePLE', 'bubbles/list'],
	[['moderator'], 'bubblePLE', 'semesters/list'],
	[['moderator'], 'bubblePLE', 'courses/list'],
	[['moderator'], 'bubblePLE', 'l2PMaterialFolders/list'],
	[['moderator'], 'bubblePLE', 'l2PMaterialAttachments/list'],
	[['moderator'], 'bubblePLE', 'attachments/list'],
	[['moderator'], 'bubblePLE', 'fileAttachments/list'],
	[['moderator'], 'bubblePLE', 'mediaAttachments/list'],
	[['moderator'], 'bubblePLE', 'imageAttachments/list'],
	[['moderator'], 'bubblePLE', 'videoAttachments/list'],
	[['moderator'], 'bubblePLE', 'linkAttachments/list'],
);

return array(
	'controllers' => array(
		'invokables' => array(
			Controller\IndexController::class => Controller\IndexController::class,
			Controller\BubbleController::class => Controller\BubbleController::class,
			Controller\SemesterController::class => Controller\SemesterController::class,
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
            Provider\Resource\Config::class => $ressources,
        ),

		
		'rule_providers' => array(
			Provider\Rule\Config::class => array(
                'allow' => $ressourceAllowRules,
                'deny' => array(),
            )
		),
		
        'guards' => array(
            Guard\Route::class => $guardConfig
		),
	),

	// language options
	'translator' => array(
		'translation_file_patterns' => array(
			array(
				'type'     => 'gettext',
				'base_dir' => __DIR__ . '/../language',
				'pattern'  => '%s.mo',
			),
		),
	),

	'service_manager' => array(
		'factories' => array(
		),
		'invokables' => array(
			Service\L2PSync::class => Service\L2PSync::class,
			Service\BubblePermission::class => Service\BubblePermission::class,
			Listener\L2PListener::class => Listener\L2PListener::class,
		),
	),
				
	// view options
	'view_manager' => array(
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
		'template_map' => array(
			'angular/layout'           => __DIR__ . '/../view/angular/layout.phtml',
		),
	),
	
	'navigation' => array(
		'admin' => array(
			array('label' => gettext_noop('BubblePLE'),       'route' => 'zfcadmin/bubbles',             'resource' => 'bubblePLE', 'privilege' => 'bubbles/list', 'pages' => array(
				array('label' => gettext_noop('Edges'),              'route' => 'zfcadmin/bubblePLE/edges'              , 'resource' => 'bubblePLE', 'privilege' => 'edges/list'),
				array('label' => gettext_noop('Bubbles'),            'route' => 'zfcadmin/bubblePLE/bubbles'            , 'resource' => 'bubblePLE', 'privilege' => 'bubbles/list'),
				array('label' => gettext_noop('Semesters'),          'route' => 'zfcadmin/bubblePLE/semesters'          , 'resource' => 'bubblePLE', 'privilege' => 'semesters/list'),
				array('label' => gettext_noop('Courses'),            'route' => 'zfcadmin/bubblePLE/courses'            , 'resource' => 'bubblePLE', 'privilege' => 'courses/list'),
				array('label' => gettext_noop('L2PMaterialFolders'), 'route' => 'zfcadmin/bubblePLE/l2pmaterialfolders' , 'resource' => 'bubblePLE', 'privilege' => 'l2PMaterialFolders/list'),
				array('label' => gettext_noop('Attachments'),        'route' => 'zfcadmin/bubblePLE/attachments'        , 'resource' => 'bubblePLE', 'privilege' => 'attachments/list'),
				array('label' => gettext_noop('FileAttachments'),    'route' => 'zfcadmin/bubblePLE/fileAttachments'    , 'resource' => 'bubblePLE', 'privilege' => 'fileAttachments/list'),
				array('label' => gettext_noop('L2PMaterialAttachments'), 'route' => 'zfcadmin/bubblePLE/l2pmaterialattachments' , 'resource' => 'bubblePLE', 'privilege' => 'l2PMaterialAttachments/list'),
				array('label' => gettext_noop('MediaAttachments'),   'route' => 'zfcadmin/bubblePLE/mediaAttachments'   , 'resource' => 'bubblePLE', 'privilege' => 'mediaAttachments/list'),
				array('label' => gettext_noop('ImageAttachments'),   'route' => 'zfcadmin/bubblePLE/imageAttachments'   , 'resource' => 'bubblePLE', 'privilege' => 'imageAttachments/list'),
				array('label' => gettext_noop('VideoAttachments'),   'route' => 'zfcadmin/bubblePLE/videoAttachments'   , 'resource' => 'bubblePLE', 'privilege' => 'videoAttachments/list'),
				array('label' => gettext_noop('LinkAttachments'),    'route' => 'zfcadmin/bubblePLE/linkAttachments'    , 'resource' => 'bubblePLE', 'privilege' => 'linkAttachments/list'),
			)),
		)
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

	),
);
