<?php
/* 
 * Copyright (C) 2014 schurix
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
use SkelletonApplication\Entity\UserProfile;

class SkelletonOptions extends AbstractOptions
{
	const REGISTRATION_METHOD_AUTO_ENABLE = 0b001;
	const REGISTRATION_METHOD_SELF_CONFIRM = 0b010;
	const REGISTRATION_METHOD_MODERATOR_CONFIRM = 0b100;
	
	const REGISTRATION_EMAIL_MODERATOR            = 0b00000001;
	const REGISTRATION_EMAIL_WELCOME              = 0b00000010;
	const REGISTRATION_EMAIL_WELCOME_CONFIRM_MAIL = 0b00000100;
	const REGISTRATION_EMAIL_CONFIRM_MAIL         = 0b00001000;
	const REGISTRATION_EMAIL_DOUBLE_CONFIRM_MAIL  = 0b00010000;
	const REGISTRATION_EMAIL_CONFIRM_MODERATOR    = 0b00100000;
	const REGISTRATION_EMAIL_ACTIVATED            = 0b01000000;
	const REGISTRATION_EMAIL_DISABLED             = 0b10000000;
	
	protected $roles = array(
			'guest' => array(),
			'user' => array(
				'moderator' => array(
					'administrator' => array() // Admin role must be leaf and must contain 'admin'
				)
			)
		);
	
	protected $userProfileEntity = UserProfile::class;
	
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

	/**
	 * List of supported languages. The key is shown in the url, the value is passed to the translator
	 * If only one language is provided, no language will be shown in the url
	 * @var array
	 */
	protected $languages = array(
		'de' => 'de_DE', 
		'en' => 'en_US'
	);
	
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
	
	public function getRoles(){
		return $this->roles;
	}

	public function setRoles($roles){
		$this->roles = $roles;
		return $this;
	}
	
	public function getUserProfileEntity() {
		return $this->userProfileEntity;
	}

	public function setUserProfileEntity($userProfileEntity) {
		$this->userProfileEntity = $userProfileEntity;
		return $this;
	}

	public function getRegistrationMethodFlag() {
		return $this->registrationMethodFlag;
	}

	public function getRegistrationEmailFlag() {
		return $this->registrationEmailFlag;
	}

	public function getRegistrationNotify() {
		return $this->registrationNotify;
	}

	public function getRegistrationNotificationFrom() {
		return $this->registrationNotificationFrom;
	}
	
	/** @return EmailOptions */
	public function getRegistrationModeratorEmail() {
		return $this->registrationModeratorEmail;
	}
	
	/** @return EmailOptions */
	public function getRegistrationUserEmailWelcome() {
		return $this->registrationUserEmailWelcome;
	}

	/** @return EmailOptions */
	public function getRegistrationUserEmailWelcomeConfirmMail() {
		return $this->registrationUserEmailWelcomeConfirmMail;
	}

	/** @return EmailOptions */
	public function getRegistrationUserEmailConfirmMail() {
		return $this->registrationUserEmailConfirmMail;
	}

	/** @return EmailOptions */
	public function getRegistrationUserEmailConfirmModerator() {
		return $this->registrationUserEmailConfirmModerator;
	}
	
	/** @return EmailOptions */
	public function getRegistrationUserEmailActivated() {
		return $this->registrationUserEmailActivated;
	}

	/** @return EmailOptions */
	public function getRegistrationUserEmailDisabled() {
		return $this->registrationUserEmailDisabled;
	}

	public function setRegistrationMethodFlag($registrationMethod) {
		$this->registrationMethodFlag = $registrationMethod;
		return $this;
	}

	public function setRegistrationEmailFlag($registrationEmailFlag) {
		$this->registrationEmailFlag = $registrationEmailFlag;
		return $this;
	}

	public function setRegistrationNotify($registrationNotify) {
		$this->registrationNotify = $registrationNotify;
		return $this;
	}

	public function setRegistrationNotificationFrom($registrationNotificationFrom) {
		$this->registrationNotificationFrom = $registrationNotificationFrom;
		return $this;
	}

	public function setRegistrationModeratorEmail($registrationModeratorEmail) {
		$this->registrationModeratorEmail = $registrationModeratorEmail;
		return $this;
	}

	public function setRegistrationUserEmailWelcome($registrationUserEmailWelcome) {
		$this->registrationUserEmailWelcome = $registrationUserEmailWelcome;
		return $this;
	}

	public function setRegistrationUserEmailWelcomeConfirmMail($registrationUserEmailWelcomeConfirmMail) {
		$this->registrationUserEmailWelcomeConfirmMail = $registrationUserEmailWelcomeConfirmMail;
		return $this;
	}

	public function setRegistrationUserEmailConfirmMail($registrationUserEmailConfirmMail) {
		$this->registrationUserEmailConfirmMail = $registrationUserEmailConfirmMail;
		return $this;
	}

	public function setRegistrationUserEmailConfirmModerator($registrationUserEmailConfirmModerator) {
		$this->registrationUserEmailConfirmModerator = $registrationUserEmailConfirmModerator;
		return $this;
	}

	public function setRegistrationUserEmailActivated($registrationUserEmailActivated) {
		$this->registrationUserEmailActivated = $registrationUserEmailActivated;
		return $this;
	}

	public function setRegistrationUserEmailDisabled($registrationUserEmailDisabled) {
		$this->registrationUserEmailDisabled = $registrationUserEmailDisabled;
		return $this;
	}
	
	public function getRegistrationUserEmailDoubleConfirm() {
		return $this->registrationUserEmailDoubleConfirm;
	}

	public function setRegistrationUserEmailDoubleConfirm($registrationUserEmailDoubleConfirm) {
		$this->registrationUserEmailDoubleConfirm = $registrationUserEmailDoubleConfirm;
		return $this;
	}
	
	public function getLanguages() {
		return $this->languages;
	}

	public function setLanguages($languages) {
		$this->languages = $languages;
		return $this;
	}

}