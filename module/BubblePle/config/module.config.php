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

$routerConfig = array(
	'test' => array(
		'type' => 'Literal',
		'options' => array(
			'route' => '/test',
			'defaults' => array(
				'controller' => Controller\IndexController::class,
				'action'     => 'blub',
			),
		),
	),
);

$guardConfig = array(
	'test' => ['route' => 'test',  'roles' => ['guest', 'user'] ],
);

$ressources = array(
	
);

$ressourceAllowRules = array(
	
);

return array(
	'controllers' => array(
		'invokables' => array(
			Controller\IndexController::class => Controller\IndexController::class,
		),
	),
	
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
			'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
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

	),
);
