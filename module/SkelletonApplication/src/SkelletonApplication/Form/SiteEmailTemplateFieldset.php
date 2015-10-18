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

namespace SkelletonApplication\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use SkelletonApplication\Options\SiteRegistrationOptions;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
/**
 * SiteEmailTemplateFieldset Fieldset
 *
 * @author schurix
 */
class SiteEmailTemplateFieldset extends Fieldset implements InputFilterProviderInterface, ObjectManagerAwareInterface, ServiceLocatorAwareInterface{
	use ProvidesObjectManager, ServiceLocatorAwareTrait;
	
	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'SiteEmailTemplateFieldset';
		}
		parent::__construct($name, $options);
	}
	
	public function init() {
		$this->addEmailFieldsets();
	}
	
	public function getInputFilterSpecification() {
		$filters = array(
		);
		return $filters;
	}
	
	protected function addEmailFieldsets(){
		/* @var $registrationOptions SiteRegistrationOptions */
		$registrationOptions = $this->getServiceLocator()->getServiceLocator()->get(SiteRegistrationOptions::class);
		$emailFlag = $registrationOptions->getRelevantEmailFlag();
		for($i = 0; (1 << $i) <= $emailFlag; $i++){
			if($emailFlag & (1 << $i)){
				$this->addEmailFieldset(1 << $i);
			}
		}
	}
	
	protected function addEmailFieldset($flag){
		$emailKey = SiteRegistrationOptions::getEmailKey($flag);
		$this->add(array(
			'name' => $emailKey,
            'type' => SiteEmailOptionsFieldset::class,
			'options' => array(
				'label' => $this->getEmailLabel($flag), 
			),
        ));
	}
	
	protected function getEmailLabel($flag){
		switch($flag){
			case SiteRegistrationOptions::REGISTRATION_EMAIL_ACTIVATED:
				return gettext_noop('Account activated');
			case SiteRegistrationOptions::REGISTRATION_EMAIL_CONFIRM_MAIL:
				return gettext_noop('Confirm mail');
			case SiteRegistrationOptions::REGISTRATION_EMAIL_CONFIRM_MODERATOR:
				return gettext_noop('Confirm moderator');
			case SiteRegistrationOptions::REGISTRATION_EMAIL_DISABLED:
				return gettext_noop('Account disabled');
			case SiteRegistrationOptions::REGISTRATION_EMAIL_DOUBLE_CONFIRM_MAIL:
				return gettext_noop('Double confirm');
			case SiteRegistrationOptions::REGISTRATION_EMAIL_MODERATOR:
				return gettext_noop('Moderator notification');
			case SiteRegistrationOptions::REGISTRATION_EMAIL_WELCOME:
				return gettext_noop('Welcome');
			case SiteRegistrationOptions::REGISTRATION_EMAIL_WELCOME_CONFIRM_MAIL:
				return gettext_noop('Welcome, confirm mail');
		}
	}
}
