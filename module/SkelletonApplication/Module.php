<?php
namespace SkelletonApplication;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Zend\View\Helper\Navigation;

class Module
{
	public function onBootstrap(MvcEvent $e){
		$app = $e->getApplication();
		$eventManager = $app->getEventManager();
		$sm = $app->getServiceManager();

		// Attach UserListener for role and UserProfile handling
		$listener = $sm->get('SkelletonApplication\UserListener');
		$eventManager->attach($listener);

		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($eventManager);

		// Enable BjyAuthorize when not in console mode
		if(!\Zend\Console\Console::isConsole()) {
			// Add ACL information to the Navigation view helper
			$authorize = $sm->get('BjyAuthorizeServiceAuthorize');
			$acl = $authorize->getAcl();
			$role = $authorize->getIdentity();
			Navigation::setDefaultAcl($acl);
			Navigation::setDefaultRole($role);		
		}
	}

	public function getConfig(){
		return include __DIR__ . '/config/module.config.php';
	}

	public function getAutoloaderConfig(){
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}

	public function getServiceConfig(){
		return array(
			'invokables' => array(
			),
			'factories' => array(
			),
		);
	}
}
