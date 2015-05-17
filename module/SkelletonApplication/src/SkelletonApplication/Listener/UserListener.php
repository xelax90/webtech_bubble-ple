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

/**
 * Description of UserEventListener
 *
 * @author schurix
 */
class UserListener extends AbstractListenerAggregate{
	
	public function attach(EventManagerInterface $events){
		$sharedManager = $events->getSharedManager();
		$this->listeners[] = $sharedManager->attach('ZfcUser\Service\User', 'register', array($this, 'onRegister'));
	}

	public function onRegister(Event $e){
		$sm = $e->getTarget()->getServiceManager();
		$em = $sm->get('doctrine.entitymanager.orm_default');
		$user = $e->getParam('user');
		$config = $sm->get('config');
		$criteria = array('roleId' => $config['zfcuser']['new_user_default_role']);
		$defaultUserRole = $em->getRepository($config['zfcuser']['role_entity_class'])->findOneBy($criteria);
		
		if ($defaultUserRole !== null){
			$user->addRole($defaultUserRole);
		}
	}
}
