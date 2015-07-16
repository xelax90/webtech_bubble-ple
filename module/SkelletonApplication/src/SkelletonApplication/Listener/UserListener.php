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

namespace SkelletonApplication\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\Event;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Creates user profile and adds default role after registration and creation
 * Adds role select to ZfcUserAdmin form
 *
 * @author schurix
 */
class UserListener extends AbstractListenerAggregate implements ServiceLocatorAwareInterface{
	/**
	 * @var ServiceLocatorInterface
	 */
	protected $serviceLocator;
	
	
	/**
	 * Attaches to ZfcUser and ZfcUserAdmin events
	 * @param EventManagerInterface $events
	 */
	public function attach(EventManagerInterface $events){
		$sharedManager = $events->getSharedManager();
		$this->listeners[] = $sharedManager->attach('ZfcUser\Service\User',         'register',        array($this, 'onRegister'));
		$this->listeners[] = $sharedManager->attach('ZfcUser\Service\User',         'register.post',   array($this, 'postRegister'));
		$this->listeners[] = $sharedManager->attach('ZfcUserAdmin\Service\User',    'create.post',     array($this, 'postRegister'));
		$this->listeners[] = $sharedManager->attach('ZfcUserAdmin\Service\User',    'edit',            array($this, 'update'));
		$this->listeners[] = $sharedManager->attach('ZfcUserAdmin\Form\CreateUser', 'init',            array($this, 'addRoleSelect'));
		$this->listeners[] = $sharedManager->attach('ZfcUserAdmin\Form\EditUser',   'init',            array($this, 'addRoleSelect'));
	}

	/**
	 * Adds the default user role to the user entity when he registers
	 * @param Event $e
	 */
	public function onRegister(Event $e){
		$sm = $this->getServiceLocator();
		/* @var $em \Doctrine\ORM\EntityManager */
		$em = $sm->get('doctrine.entitymanager.orm_default');
		/* @var $user \SkelletonApplication\Entity\User */
		$user = $e->getParam('user');
		
		$config = $sm->get('config');
		$criteria = array('roleId' => $config['zfcuser']['new_user_default_role']);
		
		/* @var $defaultUserRole \SkelletonApplication\Entity\Role */
		$defaultUserRole = $em->getRepository($config['zfcuser']['role_entity_class'])->findOneBy($criteria);
		
		if ($defaultUserRole !== null){
			$user->addRole($defaultUserRole);
		}
	}
	
	/**
	 * Creates a UserProfile instance and attaches it to the user
	 * @param Event $e
	 */
	public function postRegister(Event $e){
		$sm = $this->getServiceLocator();
		/* @var $em \Doctrine\ORM\EntityManager */
		$em = $sm->get('doctrine.entitymanager.orm_default');
		/* @var $user \SkelletonApplication\Entity\User */
		$user = $e->getParam('user');
		/* @var $options \SkelletonApplication\Options\SkelletonOptions */
		$options = $sm->get('SkelletionApplication\Options\Application');
		
		if(count($user->getProfile()) == 0){
			$profileEntity = $options->getUserProfileEntity();
			/* @var $profile \SkelletonApplication\Entity\UserProfile */
			$profile = new $profileEntity();
			$profile->setUser($user);
			$user->setProfile(array($profile));
			$em->persist($profile);
			$em->flush();
		}
	}
	
	/**
	 * On updates, set the selected user roles and create user profile if it does not exist yet.
	 * @param Event $e
	 */
	public function update(Event $e){
		$sm = $this->getServiceLocator();
		/* @var $em \Doctrine\ORM\EntityManager */
		$em = $sm->get('doctrine.entitymanager.orm_default');
		/* @var $user \SkelletonApplication\Entity\User */
		$user = $e->getParam('user');
		/* @var $options \SkelletonApplication\Options\SkelletonOptions */
		$options = $sm->get('SkelletionApplication\Options\Application');
		
		$data = $e->getParam('data');
		
		$uData = array();
		$uData['roles'] = $data['roles'];
		
		$hydrator = new DoctrineObject($em);
		$hydrator->hydrate($uData, $user);
		
		if(count($user->getProfile()) == 0){
			$profileEntity = $options->getUserProfileEntity();
			/* @var $profile \SkelletonApplication\Entity\UserProfile */
			$profile = new $profileEntity();
			$profile->setUser($user);
			$user->setProfile(array($profile));
			$em->persist($profile);
		}
		$em->flush();
	}
	
	public function addRoleSelect(Event $e){
		$sm = $this->getServiceLocator();
		/* @var $em \Doctrine\ORM\EntityManager */
		$em = $sm->get('doctrine.entitymanager.orm_default');
		/* @var $form \ZfcUser\Form\Register */
		$form = $e->getTarget();
		
		$config = $sm->get('config');
		$roleEntity = $config['zfcuser']['role_entity_class'];
		
		$form->add(
			array(
				'name' => 'roles',
				'type' => 'DoctrineModule\Form\Element\ObjectMultiCheckbox',
				'options' => array(
					'object_manager' => $em,
					'target_class'   => $roleEntity,
					'label' => 'Roles',
					'label_generator' => function($role) {
						/* @var $role \SkelletonApplication\Entity\Role */
						return str_repeat('&nbsp', 2*$role->getLevel()) . $role->getRoleId();
					},
					'label_options' => array(
						'disable_html_escape' => true,
					)
				),
			)
		);
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getServiceLocator() {
		return $this->serviceLocator;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
		$this->serviceLocator = $serviceLocator;
		return $this;
	}

}
