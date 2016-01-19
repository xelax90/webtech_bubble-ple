<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

use Zend\Session\Validator\RemoteAddr;
use Zend\Session\Validator\HttpUserAgent;

return array(
	'doctrine' => array(
		'connection' => array(
			'orm_default' => array(
				'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
				'params' => array(
					'charset' => 'utf8',
					'driverOptions' => array(1002=>'SET NAMES utf8'),
				)
			)
		)
	),
	'session_manager' => array(
		'validators' => array(
			//RemoteAddr::class,
			//HttpUserAgent::class,
		)
	),
);
