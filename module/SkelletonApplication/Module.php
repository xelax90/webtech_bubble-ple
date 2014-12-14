<?php
namespace SkelletonApplication;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use SkelletonApplication\Entity\User;

use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Doctrine\ORM\EntityManager;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
		$app = $e->getApplication();
		$eventManager = $app->getEventManager(); 
		$sm = $app->getServiceManager();
	    $em = $sm->get('doctrine.entitymanager.orm_default');
		
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

		// Protect all views except whitelist by login form
		//$this->protectViewsLogin($e);
		// Extend the ZfcUser registration form with custom fields 
		$this->extendUserRegistrationForm($eventManager, $em);	
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

	public function getServiceConfig()
	{
		return array(
            'invokables' => array(
            ),
			'factories' => array(
			),
		);
	}

	
	/**
	 * Extends the ZfcUser registration form with custom fields
	 * 
	 * @param EventManager $eventManager
	 */
	protected function extendUserRegistrationForm(EventManager $eventManager, EntityManager $em){
		// custom fields of registration form (ZfcUser)
		$sharedEvents = $eventManager->getSharedManager();
		$addFields = function($e) use ($em){
			/* @var $form \ZfcUser\Form\Register */
			$form = $e->getTarget();
			
			$form->add(
				array(
					'name' => 'roles',
					'type' => 'DoctrineModule\Form\Element\ObjectMultiCheckbox',
					'options' => array(
						'object_manager' => $em,
						'target_class'   => 'FSMPIVideo\Entity\Role',
						'label' => 'Rollen',
						'label_generator' => function($e) {
							return str_repeat('&nbsp', 2*$e->getLevel()) . $e->getRoleId();
						},
						'label_options' => array(
							'disable_html_escape' => true,
						)
					),
				)
			);
		};
		
		/*
		$sharedEvents->attach(
			'ZfcUser\Form\Register',
			'init',
			$addFields
		);
		 */

		$sharedEvents->attach(
			'ZfcUserAdmin\Form\CreateUser',
			'init',
			$addFields
		);

		$sharedEvents->attach(
			'ZfcUserAdmin\Form\EditUser',
			'init',
			$addFields
		);
	}
	
}
