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

namespace SkelletonApplication\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * SiteOptions for user registration
 *
 * @author schurix
 */
class SiteRegistrationOptions extends AbstractOptions{
	const REGISTRATION_METHOD_AUTO_ENABLE = 0b001; // user is automatically enabled after registration
	const REGISTRATION_METHOD_SELF_CONFIRM = 0b010; // user recieves an e-mail where he can confirm his address to activate himself
	const REGISTRATION_METHOD_MODERATOR_CONFIRM = 0b100; // user must be activated by moderator
	
	const REGISTRATION_EMAIL_MODERATOR            = 0b00000001;
	const REGISTRATION_EMAIL_WELCOME              = 0b00000010;
	const REGISTRATION_EMAIL_WELCOME_CONFIRM_MAIL = 0b00000100;
	const REGISTRATION_EMAIL_CONFIRM_MAIL         = 0b00001000;
	const REGISTRATION_EMAIL_DOUBLE_CONFIRM_MAIL  = 0b00010000;
	const REGISTRATION_EMAIL_CONFIRM_MODERATOR    = 0b00100000;
	const REGISTRATION_EMAIL_ACTIVATED            = 0b01000000;
	const REGISTRATION_EMAIL_DISABLED             = 0b10000000;
	
	protected $registrationMethodFlag = self::REGISTRATION_METHOD_MODERATOR_CONFIRM;
	protected $registrationEmailFlag;
	protected $registrationNotify = array('moderator', 'administrator');
	protected $registrationNotificationFrom = 'SkelletonApplication <schurix@gmx.de>';
	
	/** @var EmailOptions */
	protected $registrationModeratorEmail;
	/** @var EmailOptions */
	protected $registrationUserEmailWelcome; // Without confirmation (only auto enable)
	/** @var EmailOptions */
	protected $registrationUserEmailWelcomeConfirmMail; // Auto enable & self confirm
	/** @var EmailOptions */
	protected $registrationUserEmailConfirmMail; // Self confirm
	/** @var EmailOptions */
	protected $registrationUserEmailDoubleConfirm; // Sent after successful email confirmation when using both self confirm and moderator confirm
	/** @var EmailOptions */
	protected $registrationUserEmailConfirmModerator; // Without self confirm, with moderator confirm
	/** @var EmailOptions */
	protected $registrationUserEmailActivated; // Activated by moderator
	/** @var EmailOptions */
	protected $registrationUserEmailDisabled; // Disabled by moderator
	
	public function __construct($options = null) {
		$this->registrationEmailFlag = 
				self::REGISTRATION_EMAIL_MODERATOR | 
				self::REGISTRATION_EMAIL_WELCOME | 
				self::REGISTRATION_EMAIL_WELCOME_CONFIRM_MAIL |
				self::REGISTRATION_EMAIL_CONFIRM_MAIL | 
				self::REGISTRATION_EMAIL_DOUBLE_CONFIRM_MAIL | 
				self::REGISTRATION_EMAIL_CONFIRM_MODERATOR | 
				self::REGISTRATION_EMAIL_ACTIVATED | 
				self::REGISTRATION_EMAIL_DISABLED;
		
		
		parent::__construct($options);
		
		if(empty($this->registrationModeratorEmail)){
			$this->registrationModeratorEmail = array(
				'subject' => gettext_noop('[SkelletonApplication] A new user has registered'),
				'template' => 'skelleton-application/email/register_moderator_notification'
			);
		}
		
		if(empty($this->registrationUserEmailWelcome)){
			$this->registrationUserEmailWelcome = array(
				'subject' => gettext_noop('[SkelletonApplication] Welcome'),
				'template' => 'skelleton-application/email/register_welcome'
			);
		}
		
		if(empty($this->registrationUserEmailWelcomeConfirmMail)){
			$this->registrationUserEmailWelcomeConfirmMail = array(
				'subject' => gettext_noop('[SkelletonApplication] Welcome. Please confirm your E-Mail'),
				'template' => 'skelleton-application/email/register_welcome_confirm_mail'
			);
		}
		
		if(empty($this->registrationUserEmailDoubleConfirm)){
			$this->registrationUserEmailDoubleConfirm = array(
				'subject' => gettext_noop('[SkelletonApplication] Welcome'),
				'template' => 'skelleton-application/email/register_double_confirm_mail'
			);
		}
		
		if(empty($this->registrationUserEmailConfirmMail)){
			$this->registrationUserEmailConfirmMail = array(
				'subject' => gettext_noop('[SkelletonApplication] Welcome. Please confirm your E-Mail'),
				'template' => 'skelleton-application/email/register_confirm_mail'
			);
		}
		
		if(empty($this->registrationUserEmailConfirmModerator)){
			$this->registrationUserEmailConfirmModerator = array(
				'subject' => gettext_noop('[SkelletonApplication] Welcome'),
				'template' => 'skelleton-application/email/register_confirm_moderator'
			);
		}
		
		if(empty($this->registrationUserEmailActivated)){
			$this->registrationUserEmailActivated = array(
				'subject' => gettext_noop('[SkelletonApplication] Your Account has been verified'),
				'template' => 'skelleton-application/email/register_activated'
			);
		}
		
		if(empty($this->registrationUserEmailDisabled)){
			$this->registrationUserEmailDisabled = array(
				'subject' => gettext_noop('[SkelletonApplication] Your Account has been disabled'),
				'template' => 'skelleton-application/email/register_disabled'
			);
		}
		
		$this->registrationModeratorEmail = new EmailOptions($this->registrationModeratorEmail);
		$this->registrationUserEmailWelcome = new EmailOptions($this->registrationUserEmailWelcome);
		$this->registrationUserEmailWelcomeConfirmMail = new EmailOptions($this->registrationUserEmailWelcomeConfirmMail);
		$this->registrationUserEmailDoubleConfirm = new EmailOptions($this->registrationUserEmailDoubleConfirm);
		$this->registrationUserEmailConfirmMail = new EmailOptions($this->registrationUserEmailConfirmMail);
		$this->registrationUserEmailConfirmModerator = new EmailOptions($this->registrationUserEmailConfirmModerator);
		$this->registrationUserEmailActivated = new EmailOptions($this->registrationUserEmailActivated);
		$this->registrationUserEmailDisabled = new EmailOptions($this->registrationUserEmailDisabled);
	}
}
