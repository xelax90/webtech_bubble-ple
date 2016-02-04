<?php

/* 
 * Copyright (C) 2016 schurix
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

use XelaxAdmin\Controller\ListController;

$xelaxConfig = array(
	'edges' => array(
		'name' => 'Edge', // this will be the route url and is used to generate texts
		// You can subclass the ListController for better control
		'controller_class' => ListController::class, 
		// Base namespace of Menu entity and form
		'base_namespace' => __NAMESPACE__, 
		// columns to show in list view
		'list_columns' => array('Id' => 'id', 'From' => 'fromTitle', 'To' => 'toTitle'),
		// route_base defaults to the config key ('menus' in this case). 
		'route_base' => 'zfcadmin/bubblePLE/edges', // only available at top-level options
		'rest_enabled' => true,
	),
	'bubbles' => array(
		'name' => 'Bubble', 
		'controller_class' => Controller\BubbleController::class, 
		'base_namespace' => __NAMESPACE__, 
		'list_columns' => array('Id' => 'id', 'Title' => 'title'),
		'route_base' => 'zfcadmin/bubblePLE/bubbles',
		'rest_enabled' => true,
	),
	'semesters' => array(
		'name' => 'Semester', 
		'controller_class' => Controller\SemesterController::class, 
		'base_namespace' => __NAMESPACE__, 
		'list_columns' => array('Id' => 'id', 'Title' => 'title', 'Year' => 'year', 'Winter?' => 'isWinter'),
		'route_base' => 'zfcadmin/bubblePLE/semesters',
		'rest_enabled' => true,
	),
	'courses' => array(
		'name' => 'Course', 
		'controller_class' => Controller\BubbleController::class, 
		'base_namespace' => __NAMESPACE__, 
		'list_columns' => array('Id' => 'id', 'Title' => 'title', 'Courseroom' => 'courseroom'),
		'route_base' => 'zfcadmin/bubblePLE/courses',
		'rest_enabled' => true,
	),
	'attachments' => array(
		'name' => 'Attachment', 
		'controller_class' => Controller\BubbleController::class, 
		'base_namespace' => __NAMESPACE__, 
		'list_columns' => array('Id' => 'id', 'Title' => 'title'),
		'route_base' => 'zfcadmin/bubblePLE/attachments', 
		'rest_enabled' => true,
	),
	'fileAttachments' => array(
		'name' => 'FileAttachment', 
		'controller_class' => Controller\BubbleController::class, 
		'base_namespace' => __NAMESPACE__, 
		'list_columns' => array('Id' => 'id', 'Title' => 'title', 'File' => 'filename'),
		'route_base' => 'zfcadmin/bubblePLE/fileAttachments', 
		'rest_enabled' => true,
	),
	'mediaAttachments' => array(
		'name' => 'MediaAttachment', 
		'controller_class' => Controller\BubbleController::class, 
		'base_namespace' => __NAMESPACE__, 
		'list_columns' => array('Id' => 'id', 'Title' => 'title', 'File' => 'filename'),
		'route_base' => 'zfcadmin/bubblePLE/mediaAttachments',
		'rest_enabled' => true,
	),
	'imageAttachments' => array(
		'name' => 'ImageAttachment', 
		'controller_class' => Controller\BubbleController::class, 
		'base_namespace' => __NAMESPACE__, 
		'list_columns' => array('Id' => 'id', 'Title' => 'title', 'File' => 'filename'),
		'route_base' => 'zfcadmin/bubblePLE/imageAttachments',
		'rest_enabled' => true,
	),
	'videoAttachments' => array(
		'name' => 'VideoAttachment', 
		'controller_class' => Controller\BubbleController::class, 
		'base_namespace' => __NAMESPACE__, 
		'list_columns' => array('Id' => 'id', 'Title' => 'title', 'File' => 'filename'),
		'route_base' => 'zfcadmin/bubblePLE/videoAttachments',
		'rest_enabled' => true,
	),
	'linkAttachments' => array(
		'name' => 'LinkAttachment', 
		'controller_class' => Controller\BubbleController::class, 
		'base_namespace' => __NAMESPACE__, 
		'list_columns' => array('Id' => 'id', 'Title' => 'title', 'URL' => 'url'),
		'route_base' => 'zfcadmin/bubblePLE/linkAttachments',
		'rest_enabled' => true,
	),
);

return $xelaxConfig;