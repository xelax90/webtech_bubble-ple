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
use XelaxAdmin\Controller\ListController;
use XelaxAdmin\Router\ListRoute;

$xelaxConfig = array(
	/*
	 * Configure your list controllers. Routes are generated automatically, and
	 * access permissions can be configured.
	 */
	'list_controller' => array(
		'bubbles' => array(
			'name' => 'Bubble', // this will be the route url and is used to generate texts
			// You can subclass the ListController for better control
			'controller_class' => ListController::class, 
			// Base namespace of Menu entity and form
			'base_namespace' => 'BubblePle', 
			// columns to show in list view
			'list_columns' => array('Id' => 'id', 'Title' => 'title'),
			// route_base defaults to the config key ('menus' in this case). 
			'route_base' => 'zfcadmin/bubblePLE/bubbles', // only available at top-level options
		),
		'attachments' => array(
			'name' => 'Attachment', // this will be the route url and is used to generate texts
			// You can subclass the ListController for better control
			'controller_class' => ListController::class, 
			// Base namespace of Menu entity and form
			'base_namespace' => 'BubblePle', 
			// columns to show in list view
			'list_columns' => array('Id' => 'id', 'Title' => 'title'),
			// route_base defaults to the config key ('menus' in this case). 
			'route_base' => 'zfcadmin/bubblePLE/attachments', // only available at top-level options
		),
		'fileAttachments' => array(
			'name' => 'FileAttachment', // this will be the route url and is used to generate texts
			// You can subclass the ListController for better control
			'controller_class' => ListController::class, 
			// Base namespace of Menu entity and form
			'base_namespace' => 'BubblePle', 
			// columns to show in list view
			'list_columns' => array('Id' => 'id', 'Title' => 'title', 'File' => 'filename'),
			// route_base defaults to the config key ('menus' in this case). 
			'route_base' => 'zfcadmin/bubblePLE/fileAttachments', // only available at top-level options
		),
		'edges' => array(
			'name' => 'Edge', // this will be the route url and is used to generate texts
			// You can subclass the ListController for better control
			'controller_class' => ListController::class, 
			// Base namespace of Menu entity and form
			'base_namespace' => 'BubblePle', 
			// columns to show in list view
			'list_columns' => array('Id' => 'id', 'From' => 'fromTitle', 'To' => 'toTitle'),
			// route_base defaults to the config key ('menus' in this case). 
			'route_base' => 'zfcadmin/bubblePLE/edges', // only available at top-level options
		),
	),
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
				'controller' => Controller\IndexController::class,
				'action'     => 'index',
			),
		),
	),
	
	'zfcadmin' => array(
		'child_routes' => array(
			'bubblePLE' => array(
				'type' => 'Literal',
				'options' => array(
					'route' => '/test',
				),
				'may_terminate' => false,
				'child_routes' => array(
					'bubbles'         => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'bubbles', )),
					'attachments'     => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'attachments', )),
					'fileAttachments' => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'fileAttachments', )),
					'edges'           => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'edges', )),
				)
			),
		)
	),
);

$guardConfig = array(
	'test' => ['route' => 'test', 'roles' => ['guest', 'user'] ],
	['route' => 'zfcadmin/bubblePLE/bubbles',         'roles' => ['moderator'] ],
	['route' => 'zfcadmin/bubblePLE/attachments',     'roles' => ['moderator'] ],
	['route' => 'zfcadmin/bubblePLE/fileAttachments', 'roles' => ['moderator'] ],
	['route' => 'zfcadmin/bubblePLE/edges',           'roles' => ['moderator'] ],
);

$ressources = array(
	'bubblePLE',
);

$ressourceAllowRules = array(
	[['moderator'], 'bubblePLE', 'bubbles/list'],
	[['moderator'], 'bubblePLE', 'attachments/list'],
	[['moderator'], 'bubblePLE', 'fileAttachments/list'],
	[['moderator'], 'bubblePLE', 'edges/list'],
	
);

return array(
	'controllers' => array(
		'invokables' => array(
			Controller\IndexController::class => Controller\IndexController::class,
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
				array('label' => gettext_noop('Edges'),           'route' => 'zfcadmin/bubblePLE/edges'           , 'resource' => 'bubblePLE', 'privilege' => 'edges/list'),
				array('label' => gettext_noop('Bubbles'),         'route' => 'zfcadmin/bubblePLE/bubbles'         , 'resource' => 'bubblePLE', 'privilege' => 'bubbles/list'),
				array('label' => gettext_noop('Attachments'),     'route' => 'zfcadmin/bubblePLE/attachments'     , 'resource' => 'bubblePLE', 'privilege' => 'attachments/list'),
				array('label' => gettext_noop('FileAttachments'), 'route' => 'zfcadmin/bubblePLE/fileAttachments' , 'resource' => 'bubblePLE', 'privilege' => 'fileAttachments/list'),
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
