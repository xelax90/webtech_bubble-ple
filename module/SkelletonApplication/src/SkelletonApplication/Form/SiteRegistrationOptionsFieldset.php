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
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use SkelletonApplication\Options\SiteRegistrationOptions;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * SiteRegistrationOptionsFieldset Fieldset
 *
 * @author schurix
 */
class SiteRegistrationOptionsFieldset extends Fieldset implements InputFilterProviderInterface, ServiceLocatorAwareInterface, ObjectManagerAwareInterface{
	use ProvidesObjectManager, ServiceLocatorAwareTrait;
	
	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'SiteRegistrationOptionsFieldset';
		}
		parent::__construct($name, $options);
	}
	
	public function init(){
		$sm = $this->getServiceLocator()->getServiceLocator();
		
		$this->add(array(
            'name' => 'registration_method_flag',
            'type' => 'select',
            'options' => array(
                'label' => gettext_noop('Registration Method'),
                'value_options' => array(
					SiteRegistrationOptions::REGISTRATION_METHOD_AUTO_ENABLE       
						=> gettext_noop('Auto enable'),
					SiteRegistrationOptions::REGISTRATION_METHOD_SELF_CONFIRM      
						=> gettext_noop('Self confirm'),
					SiteRegistrationOptions::REGISTRATION_METHOD_MODERATOR_CONFIRM 
						=> gettext_noop('Moderator confirm'),
					SiteRegistrationOptions::REGISTRATION_METHOD_AUTO_ENABLE | 
						SiteRegistrationOptions::REGISTRATION_METHOD_SELF_CONFIRM      
						=> gettext_noop('Auto enable + Self confirm'),
					SiteRegistrationOptions::REGISTRATION_METHOD_SELF_CONFIRM | 
						SiteRegistrationOptions::REGISTRATION_METHOD_MODERATOR_CONFIRM     
						=> gettext_noop('Self confirm + Moderator confirm'),
					SiteRegistrationOptions::REGISTRATION_METHOD_AUTO_ENABLE | 
						SiteRegistrationOptions::REGISTRATION_METHOD_SELF_CONFIRM | 
						SiteRegistrationOptions::REGISTRATION_METHOD_MODERATOR_CONFIRM 
						=> gettext_noop('All together'),
				),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
            )
        ));
		
		$this->add(array(
            'name' => 'registration_email_flag',
            'type' => 'select',
            'options' => array(
                'label' => gettext_noop('E-Mail notifications'),
                'value_options' => array(
					SiteRegistrationOptions::REGISTRATION_EMAIL_MODERATOR       
						=> gettext_noop('Notify moderator'),
					SiteRegistrationOptions::REGISTRATION_EMAIL_WELCOME      
						=> gettext_noop('Welcome (Only auto enable)'),
					SiteRegistrationOptions::REGISTRATION_EMAIL_WELCOME_CONFIRM_MAIL 
						=> gettext_noop('Welcome confirm (Auto enable & self confirm)'),
					SiteRegistrationOptions::REGISTRATION_EMAIL_CONFIRM_MAIL 
						=> gettext_noop('Self Confirm'),
					SiteRegistrationOptions::REGISTRATION_EMAIL_DOUBLE_CONFIRM_MAIL 
						=> gettext_noop('Double confirm (After successful confirmation with Self + Moderator confirm)'),
					SiteRegistrationOptions::REGISTRATION_EMAIL_CONFIRM_MODERATOR 
						=> gettext_noop('Moderator confirm'),
					SiteRegistrationOptions::REGISTRATION_EMAIL_ACTIVATED 
						=> gettext_noop('Activated'),
					SiteRegistrationOptions::REGISTRATION_EMAIL_DISABLED 
						=> gettext_noop('Disabled'),
				),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
            ),
			'attributes' => array(
				'multiple' => 'multiple',
				'data-fancy' => '1',
			),
        ));
		
		$config = $sm->get('config');
		$roleEntity = $config['zfcuser']['role_entity_class'];
		
		$this->add(array(
			'name' => 'registration_notify',
			'type' => 'DoctrineModule\Form\Element\ObjectMultiCheckbox',
			'options' => array(
				'object_manager' => $this->getObjectManager(),
				'target_class'   => $roleEntity,
				'label' => gettext_noop('Registration notification'),
				'label_generator' => function($role) {
					/* @var $role \SkelletonApplication\Entity\Role */
					return str_repeat('&nbsp', 2*$role->getLevel()) . $role->getRoleId();
				},
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
				'label_options' => array(
					'disable_html_escape' => true,
				)
			),
		));
		
		$this->add(array(
			'name' => 'registration_notification_from',
			'type' => 'Text',
			'options' => array(
				'label' => gettext_noop('Registration notification from'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
			)
		));
		
		$this->add(array(
			'name' => 'registration_moderator_email',
            'type' => SiteEmailOptionsFieldset::class,
            'options' => array(
				'label' => gettext_noop('E-Mail Moderator notification'),
                'use_as_base_fieldset' => false,
            ),
        ));
		
		$this->add(array(
			'name' => 'registration_user_email_welcome_confirm_mail',
            'type' => SiteEmailOptionsFieldset::class,
            'options' => array(
				'label' => gettext_noop('E-Mail Welcome confirm'),
                'use_as_base_fieldset' => false,
            ),
        ));
		
		$this->add(array(
			'name' => 'registration_user_email_double_confirm',
            'type' => SiteEmailOptionsFieldset::class,
            'options' => array(
				'label' => gettext_noop('E-Mail Double confirm'),
                'use_as_base_fieldset' => false,
            ),
        ));
		
		$this->add(array(
			'name' => 'registration_user_email_confirm_mail',
            'type' => SiteEmailOptionsFieldset::class,
            'options' => array(
				'label' => gettext_noop('E-Mail Self confirm'),
                'use_as_base_fieldset' => false,
            ),
        ));
		
		$this->add(array(
			'name' => 'registration_user_email_confirm_moderator',
            'type' => SiteEmailOptionsFieldset::class,
            'options' => array(
				'label' => gettext_noop('E-Mail Moderator confirm'),
                'use_as_base_fieldset' => false,
            ),
        ));
		
		$this->add(array(
			'name' => 'registration_user_email_activated',
            'type' => SiteEmailOptionsFieldset::class,
            'options' => array(
				'label' => gettext_noop('E-Mail Activated'),
                'use_as_base_fieldset' => false,
            ),
        ));
		
		$this->add(array(
			'name' => 'registration_user_email_disabled',
            'type' => SiteEmailOptionsFieldset::class,
            'options' => array(
				'label' => gettext_noop('E-Mail Disabled'),
                'use_as_base_fieldset' => false,
            ),
        ));
	}
	
	public function getInputFilterSpecification() {
		$filters = array(
			'registrationMethodFlag' => array(
				'required' => true,
			),
			
		);
		return $filters;
	}
}
