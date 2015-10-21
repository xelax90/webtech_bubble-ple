<?php
namespace SkelletonApplication;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use SkelletonApplication\Listener\UserListener;
use Zend\View\Helper\Navigation;
use BjyAuthorize\Service\Authorize;

class Module
{
	public function onBootstrap(MvcEvent $e){
		$app = $e->getApplication();
		$eventManager = $app->getEventManager();
		$sm = $app->getServiceManager();

		// Attach UserListener for role and UserProfile handling
		$listener = $sm->get(UserListener::class);
		$eventManager->attach($listener);

		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($eventManager);
		$eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'initTranslator'));
		$eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'setUserLanguage'));
		
		// Enable BjyAuthorize when not in console mode
		if(!\Zend\Console\Console::isConsole()) {
			// Add ACL information to the Navigation view helper
			$authorize = $sm->get(Authorize::class);
			$acl = $authorize->getAcl();
			$role = $authorize->getIdentity();
			Navigation::setDefaultAcl($acl);
			Navigation::setDefaultRole($role);		
		}
		
		if($e->getRouter() instanceof \Zend\Mvc\Router\Http\TranslatorAwareTreeRouteStack){
			/* @var $translator \Zend\I18n\Translator\Translator */
			$translator = $e->getApplication()->getServiceManager()->get('MvcTranslator');
			$e->getRouter()->setTranslator($translator);
		}
	}
	
	public function initTranslator(MvcEvent $e){
		$languages = array(
			'de' => 'de_DE',
			'en' => 'en_US'
		);
		
		/* @var $translator \Zend\I18n\Translator\Translator */
		$translator = $e->getApplication()->getServiceManager()->get('MvcTranslator');
		
		// add Db Loader factory
		$translator->getPluginManager()->setFactory(I18n\Translator\Loader\Db::class, I18n\Translator\Loader\Factory\DbFactory::class);
		
		$routeMatch = $e->getRouteMatch();
		if(!$routeMatch){
			return;
		}
		
		$lang = $routeMatch->getParam('locale');
		if(!$lang || !in_array($lang, $languages)){
			return;
		}
		$translator->setLocale($lang);
	}

	public function setUserLanguage(MvcEvent $e){
		/* @var $translator \Zend\I18n\Translator\Translator */
		$translator = $e->getApplication()->getServiceManager()->get('MvcTranslator');
		/* @var $authService \Zend\Authentication\AuthenticationService */
		$authService = $e->getApplication()->getServiceManager()->get('zfcuser_auth_service');
		
		if($authService->hasIdentity()){
			$user = $authService->getIdentity();
			if(is_callable(array($user, 'setLocale'))){
				/* @var $em \Doctrine\ORM\EntityManager */
				$em = $e->getApplication()->getServiceManager()->get(\Doctrine\ORM\EntityManager::class);
				$user->setLocale($translator->getLocale());
				$em->flush();
			}
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
