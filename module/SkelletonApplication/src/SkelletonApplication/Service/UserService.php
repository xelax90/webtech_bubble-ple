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

namespace SkelletonApplication\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use SkelletonApplication\Entity\User;
use Doctrine\ORM\EntityManager;
use SkelletonApplication\Options\SiteRegistrationOptions;

/**
 * Description of UserService
 *
 * @author schurix
 */
class UserService implements ServiceLocatorAwareInterface{
	use ServiceLocatorAwareTrait;
	
	/** @var EntityManager */
	protected $em;
	
	/**
	 * @param EntityManager $em
	 */
	public function setEntityManager(EntityManager $em){
		$this->em = $em;
	}
	
	/**
	 * @return EntityManager
	 */
	public function getEntityManager(){
		if (null === $this->em) {
			$this->em = $this->getServiceLocator()->get(EntityManager::class);
		}
		return $this->em;
	}
	
	public function cleanExpiredVerificationRequests($expiryTime = 86400){
		/* @var $userRepo \SkelletonApplication\Model\UserRepository */
		$userRepo = $this->getEntityManager()->getRepository(User::class);
		
		// remove old, not verified records 
		return $userRepo->cleanExpiredVerificationRequests($expiryTime);
	}
	
	public function findByToken($token){
		/* @var $userRepo \SkelletonApplication\Model\UserRepository */
		$userRepo = $this->getEntityManager()->getRepository(User::class);
		
		return $userRepo->findByToken($token);
	}
	
	/**
	 * 
	 * @param User $user
	 */
	public function activateUser($user){
		/* @var $options SiteRegistrationOptions */
		$options = $this->getServiceLocator()->get(SiteRegistrationOptions::class);
		
		$user->setIsActive(true);
		$this->getEntityManager()->flush($user);
		if($options->getRegistrationEmailFlag() & SiteRegistrationOptions::REGISTRATION_EMAIL_ACTIVATED){
			/* @var $transport \GoalioMailService\Mail\Service\Message */
			$transport = $this->getServiceLocator()->get('goaliomailservice_message');
			
			$email = $options->getRegistrationUserEmailActivated();
			$message = $transport->createHtmlMessage($options->getRegistrationNotificationFrom(), $user->getEmail(), $email->getSubject(), $email->getTemplate(), array('user' => $user));
			$transport->send($message);
		}
	}
	
	/**
	 * 
	 * @param User $user
	 */
	public function disableUser($user){
		/* @var $options SiteRegistrationOptions */
		$options = $this->getServiceLocator()->get(SiteRegistrationOptions::class);
		
		$user->setIsActive(false);
		$this->getEntityManager()->flush($user);
		if($options->getRegistrationEmailFlag() & SiteRegistrationOptions::REGISTRATION_EMAIL_DISABLED){
			/* @var $transport \GoalioMailService\Mail\Service\Message */
			$transport = $this->getServiceLocator()->get('goaliomailservice_message');
			
			$email = $options->getRegistrationUserEmailDisabled();
			$message = $transport->createHtmlMessage($options->getRegistrationNotificationFrom(), $user->getEmail(), $email->getSubject(), $email->getTemplate(), array('user' => $user));
			$transport->send($message);
		}
	}
}
