<?php
namespace SkelletonApplication;

use Zend\Cache\Service\StorageCacheAbstractServiceFactory;
use Zend\Log\LoggerAbstractServiceFactory;
use ZfcUser\Authentication\Storage\Db as ZfcDbStorage;
use Zend\Navigation\Service\DefaultNavigationFactory;
use Zend\ServiceManager\ServiceManager;

return array(
	'abstract_factories' => array(
		StorageCacheAbstractServiceFactory::class,
		LoggerAbstractServiceFactory::class,
	),
	'invokables' => array(
		Listener\UserListener::class => Listener\UserListener::class,
		Service\UserService::class => Service\UserService::class,
		Service\UserNotificationService::class => Service\UserNotificationService::class,
		ZfcDbStorage::class => Authentication\Storage\Db::class,
	),
	'factories' => array(
		'Navigation' => DefaultNavigationFactory::class,
		Options\SkelletonOptions::class => function (ServiceManager $sm) {
			$config = $sm->get('Config');
			return new Options\SkelletonOptions(isset($config['skelleton_application']) ? $config['skelleton_application'] : array());
		},
		'zfcuser_module_options' => Options\Service\ZfcUserOptionsFactory::class,
		Options\SiteRegistrationOptions::class => Options\Service\SiteRegistrationOptionsFactory::class,
		Twig\DbLoader::class => Twig\DbLoaderFactory::class,
	),
	'aliases' => array(
		'SkelletonApplication\Options\Application' => Options\SkelletonOptions::class,
		'SkelletonApplication\UserListener' => Listener\UserListener::class,
		'SkelletonApplication\UserService' => Service\UserService::class,
		'translator' => 'MvcTranslator'
	)
);