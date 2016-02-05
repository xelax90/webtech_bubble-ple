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

namespace BubblePle\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\Event;

/**
 * Description of L2PListener
 *
 * @author schurix
 */
class L2PListener extends AbstractListenerAggregate implements ServiceLocatorAwareInterface{
	use ServiceLocatorAwareTrait;
	
	public function attach(EventManagerInterface $events){
		$sharedManager = $events->getSharedManager();
		$this->listeners[] = $sharedManager->attach('L2PClientModule\Controller\TokenController',         'l2p.authorized',        array($this, 'onAuthorize'));
	}
	
	public function onAuthorize(Event $e){
		$sm = $this->getServiceLocator();
		/* @var $auth \Zend\Authentication\AuthenticationService */
		$auth = $sm->get('zfcuser_auth_service');
		if($auth->hasIdentity()){
			return;
		}
		
		/* @var $userService \ZfcUser\Service\User */
		$userService = $sm->get('zfcuser_user_service');
		/* @var $userMapper \ZfcUser\Mapper\User */
		$userMapper = $sm->get('zfcuser_user_mapper');
		$user = $userService->register(array(
			'username' => 'dummy',
			'display_name' => 'dummy',
			'email' => 'dummy@dummy.de',
			'password' => 'dummybabaa',
			'passwordVerify' => 'dummybabaa'
		));
		if(!$user){
			return;
		}
		$user->setUsername(null);
		$user->setDisplayName(null);
		$user->setEmail(null);
		$user->setPassword('');
		$userMapper->update($user);
		
		$auth->getStorage()->write($user->getId());
	}
}
