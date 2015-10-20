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
use Doctrine\ORM\EntityManager;
use GoalioMailService\Mail\Service\Message as GoalioMessage;
use SkelletonApplication\Options\SiteRegistrationOptions;
use Zend\Mail\Message;

/**
 * Service that handles user notifications
 *
 * @author schurix
 */
class UserNotificationService implements ServiceLocatorAwareInterface{
	use ServiceLocatorAwareTrait;
	
	const EVENT_REGISTER = 'register.post';
	const EVENT_TOKEN = 'check-token.post';
	const EVENT_ACTIVATED = 'activate.post';
	const EVENT_DISABLED = 'disable.post';
	
	/** @var GoalioMessage */
	protected $transport;
	/** @var SiteRegistrationOptions */
	protected $registrationOptions;
	/** @var EntityManager */
	protected $entityManager;
	/** @var \Zend\Mvc\I18n\Translator */
	protected $translator;
	
	public function notifyUser($user, $event){
		switch($event){
			case static::EVENT_REGISTER:
				return $this->notifyUserRegister($user);
			case static::EVENT_TOKEN:
				return $this->notifyUserToken($user);
			case static::EVENT_ACTIVATED:
				return $this->notifyUserActivated($user);
			case static::EVENT_DISABLED:
				return $this->notifyUserDisabled($user);
		}
	}
	
	protected function notifyUserRegister($user){
		$options = $this->getRegistrationOptions();
		
		$message = null;
		$flag = $options->getRegistrationMethodFlag();
		if($flag === SiteRegistrationOptions::REGISTRATION_METHOD_AUTO_ENABLE){
			$message = $this->getMessage(SiteRegistrationOptions::REGISTRATION_EMAIL_WELCOME, $user);
		} elseif(($flag & SiteRegistrationOptions::REGISTRATION_METHOD_AUTO_ENABLE) && ($flag & SiteRegistrationOptions::REGISTRATION_METHOD_SELF_CONFIRM)){
			$message = $this->getMessage(SiteRegistrationOptions::REGISTRATION_EMAIL_WELCOME_CONFIRM_MAIL, $user);
		} elseif($flag & SiteRegistrationOptions::REGISTRATION_METHOD_SELF_CONFIRM){
			$message = $this->getMessage(SiteRegistrationOptions::REGISTRATION_EMAIL_CONFIRM_MAIL, $user);
		} elseif($flag & SiteRegistrationOptions::REGISTRATION_METHOD_MODERATOR_CONFIRM){
			$message = $this->getMessage(SiteRegistrationOptions::REGISTRATION_EMAIL_CONFIRM_MODERATOR, $user);
		}
		
		$result = $this->sendMessage($message);
		
		
		if(
			// Send moderator notification if method is not double confirm (on double confirm it is sent after email is verified)
			$flag !== (SiteRegistrationOptions::REGISTRATION_METHOD_SELF_CONFIRM | SiteRegistrationOptions::REGISTRATION_METHOD_MODERATOR_CONFIRM) &&
			$options->getRegistrationEmailFlag() & SiteRegistrationOptions::REGISTRATION_EMAIL_MODERATOR
		){
			$this->notifyModerators($user);
		}
		return $result;
	}
	
	protected function notifyUserToken($user){
		$options = $this->getRegistrationOptions();
		if(
			// send moderator and doubleConfirm only when method is doubleConfirm
			$options->getRegistrationMethodFlag() === (SiteRegistrationOptions::REGISTRATION_METHOD_SELF_CONFIRM | SiteRegistrationOptions::REGISTRATION_METHOD_MODERATOR_CONFIRM)
		){
			$message = $this->getMessage(SiteRegistrationOptions::REGISTRATION_EMAIL_DOUBLE_CONFIRM_MAIL, $user);
			$result = $this->sendMessage($message);
			if($options->getRegistrationEmailFlag() & SiteRegistrationOptions::REGISTRATION_EMAIL_MODERATOR){
				$this->notifyModerators($user);
			}
			return $result;
		}
		return true;
	}
	
	protected function notifyUserActivated($user){
		$message = $this->getMessage(SiteRegistrationOptions::REGISTRATION_EMAIL_ACTIVATED, $user);
		return $this->sendMessage($message);
	}
	
	protected function notifyUserDisabled($user){
		$message = $this->getMessage(SiteRegistrationOptions::REGISTRATION_EMAIL_DISABLED, $user);
		return $this->sendMessage($message);
	}
	
	protected function notifyModerators($user){
		$options = $this->getRegistrationOptions();
		
		$roleString = true;
		foreach($options->getRegistrationNotify() as $v){
			if(is_numeric($v)){
				$roleString = false;
				break;
			}
		}

		$users = $this->getEntityManager()->getRepository(get_class($user))->createQueryBuilder('u')
				->leftJoin('u.roles', 'r');
		if($roleString){
			$users->andWhere('r.roleId IN (:roleIds)');
		} else {
			$users->andWhere('r.id IN (:roleIds)');
		}
		$users->setParameter('roleIds', $options->getRegistrationNotify());
		$mods = $users->getQuery()->getResult();
		foreach($mods as $mod){
			$message = $this->getMessage(SiteRegistrationOptions::REGISTRATION_EMAIL_MODERATOR, $mod, array('user' => $user, 'moderator' => $mod));
			$this->sendMessage($message);
		}
	}
	
	protected function sendMessage($message){
		try{
			if($message instanceof Message){
				$this->getTransport()->send($message);
				return true;
			}
		} catch (Exception $ex) {}
		return false;
	}
	
	public function getMessage($flag, $user, $parameters = null){
		if(!$this->getRegistrationOptions()->getRegistrationEmailFlag() & $flag){
			return null;
		}
		if($parameters === null){
			$parameters = array('user' => $user);
		}
		$templateKey = SiteRegistrationOptions::getEmailTemplateKey($flag);
		if(!$templateKey){
			return null;
		}
		$translator = $this->getTranslator();
		// TODO use-based language
		$options = $this->getRegistrationOptions();
		$transport = $this->getTransport();
		$message = $transport->createHtmlMessage(
				$options->getRegistrationNotificationFrom(), 
				$user->getEmail(), 
				$translator->translate(SiteRegistrationOptions::getSubjectTemplateKey($flag)),
				SiteRegistrationOptions::getEmailTemplateKey($flag),
				$parameters
		);
		return $message;
	}
	
	/**
	 * @return GoalioMessage
	 */
	public function getTransport(){
		if(null === $this->transport){
			$this->transport = $this->getServiceLocator()->get('goaliomailservice_message');
			$twigRenderer = $this->getServiceLocator()->get('ZfcTwigRenderer');
			$this->transport->setRenderer($twigRenderer);
		}
		return $this->transport;
	}
	
	/**
	 * @return EntityManager
	 */
	public function getEntityManager(){
		if(null === $this->entityManager){
			$this->entityManager = $this->getServiceLocator()->get(EntityManager::class);
		}
		return $this->entityManager;
	}
	
	/**
	 * @return SiteRegistrationOptions
	 */
	public function getRegistrationOptions(){
		if(null === $this->registrationOptions){
			$this->registrationOptions = $this->getServiceLocator()->get(SiteRegistrationOptions::class);
		}
		return $this->registrationOptions;
	}
	
	/**
	 * @return \Zend\Mvc\I18n\Translator
	 */
	public function getTranslator(){
		if(null === $this->translator){
			$this->translator = $this->getServiceLocator()->get('translator');
		}
		return $this->translator;
	}
	
	
}
