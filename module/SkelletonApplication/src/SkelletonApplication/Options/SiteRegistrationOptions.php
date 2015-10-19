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

use XelaxSiteConfig\Options\AbstractSiteOptions;

/**
 * SiteOptions for user registration
 *
 * @author schurix
 */
class SiteRegistrationOptions extends AbstractSiteOptions{
	const REGISTRATION_METHOD_AUTO_ENABLE = 0b001; // user is automatically enabled after registration
	const REGISTRATION_METHOD_SELF_CONFIRM = 0b010; // user recieves an e-mail where he can confirm his address to activate himself
	const REGISTRATION_METHOD_MODERATOR_CONFIRM = 0b100; // user must be activated by moderator
	
	const REGISTRATION_EMAIL_MODERATOR            = 0b00000001; // Moderator notification
	const REGISTRATION_EMAIL_WELCOME              = 0b00000010; // Without confirmation (only auto enable)
	const REGISTRATION_EMAIL_WELCOME_CONFIRM_MAIL = 0b00000100; // Auto enable & self confirm
	const REGISTRATION_EMAIL_CONFIRM_MAIL         = 0b00001000; // Self confirm
	const REGISTRATION_EMAIL_DOUBLE_CONFIRM_MAIL  = 0b00010000; // Sent after successful email confirmation when using both self confirm and moderator confirm
	const REGISTRATION_EMAIL_CONFIRM_MODERATOR    = 0b00100000; // Without self confirm, with moderator confirm
	const REGISTRATION_EMAIL_ACTIVATED            = 0b01000000; // Activated by moderator
	const REGISTRATION_EMAIL_DISABLED             = 0b10000000; // Disabled by moderator
	
	protected $registrationMethodFlag = self::REGISTRATION_METHOD_MODERATOR_CONFIRM;
	protected $registrationEmailFlag;
	protected $registrationNotify = array('moderator', 'administrator');
	protected $registrationNotificationFrom = 'schurix@gmx.de';
	
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
	}
	
	public function toArray() {
		$res = parent::toArray();
		foreach ($res as $key => $value) {
			if($value instanceof AbstractSiteOptions){
				$res[$key] = $value->toArray();
			}
		}
		
		$flag = $res['registration_email_flag'];
		$resFlag = array();
		$currBit = 0;
		while($flag){
			if($flag & 1){
				$resFlag[] = 1 << $currBit;
			}
			$flag >>= 1;
			$currBit++;
		}
		$res['registration_email_flag'] = $resFlag;
		
		return $res;
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

	public function setRegistrationMethodFlag($registrationMethodFlag) {
		if(is_array($registrationMethodFlag)){
			$flag = 0;
			foreach($registrationMethodFlag as $flg){
				$flag |= $flg;
			}
			$this->registrationMethodFlag = (int) $flag;
		} else {
			$this->registrationMethodFlag = (int) $registrationMethodFlag;
		}
		return $this;
	}

	public function setRegistrationEmailFlag($registrationEmailFlag) {
		if(is_array($registrationEmailFlag)){
			$flag = 0;
			foreach($registrationEmailFlag as $flg){
				$flag |= $flg;
			}
			$this->registrationEmailFlag = (int) $flag;
		} else {
			$this->registrationEmailFlag = (int) $registrationEmailFlag;
		}
		
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

	protected static function getTemplateSuffix($flag){
		$suffix = '';
		switch($flag){
			case static::REGISTRATION_EMAIL_WELCOME:
				$suffix = 'welcome';
				break;
			case static::REGISTRATION_EMAIL_WELCOME_CONFIRM_MAIL:
				$suffix = 'welcome_confirm';
				break;
			case static::REGISTRATION_EMAIL_CONFIRM_MAIL:
				$suffix = 'confirm';
				break;
			case static::REGISTRATION_EMAIL_CONFIRM_MODERATOR:
				$suffix = 'confirm_moderator';
				break;
			case static::REGISTRATION_EMAIL_DOUBLE_CONFIRM_MAIL:
				$suffix = 'double_confirm';
				break;
			case static::REGISTRATION_EMAIL_MODERATOR:
				$suffix = 'moderator';
				break;
			case static::REGISTRATION_EMAIL_ACTIVATED:
				$suffix = 'activated';
				break;
			case static::REGISTRATION_EMAIL_DISABLED:
				$suffix = 'disabled';
				break;
			default:
				return null;
				
		}
		return $suffix;
	}
	
	protected static function getTemplateKey($flag, $type){
		return static::getEmailKey($flag).'.'.$type;
	}
	
	public static function getEmailKey($flag){
		$suffix = static::getTemplateSuffix($flag);
		if(!$suffix){
			return null;
		}
		return 'skelleton.registration.email.'.$suffix;
	}
	
	public static function getEmailTemplateKey($flag){
		return static::getTemplateKey($flag, 'template');
	}
	
	public static function getSubjectTemplateKey($flag){
		return static::getTemplateKey($flag, 'subject');
	}
	
	/**
	 * Computes which emails are relevant in the current registration method/email configuration
	 * @return int
	 */
	public function getRelevantEmailFlag(){
		$methodFlag = $this->getRegistrationMethodFlag();
		$emailFlag = $this->getRegistrationEmailFlag();
		
		$relevantEmailFlag = 0;
		$relevantEmailFlag |= static::REGISTRATION_EMAIL_MODERATOR;
		$relevantEmailFlag |= static::REGISTRATION_EMAIL_ACTIVATED;
		$relevantEmailFlag |= static::REGISTRATION_EMAIL_DISABLED;
		
		switch($methodFlag){
			case static::REGISTRATION_METHOD_AUTO_ENABLE:
				$relevantEmailFlag |= static::REGISTRATION_EMAIL_WELCOME;
				break;
			case static::REGISTRATION_METHOD_SELF_CONFIRM:
				$relevantEmailFlag |= static::REGISTRATION_EMAIL_CONFIRM_MAIL;
				break;
			case static::REGISTRATION_METHOD_MODERATOR_CONFIRM:
				$relevantEmailFlag |= static::REGISTRATION_EMAIL_CONFIRM_MODERATOR;
				break;
			case static::REGISTRATION_METHOD_AUTO_ENABLE | static::REGISTRATION_METHOD_SELF_CONFIRM:
				$relevantEmailFlag |= static::REGISTRATION_EMAIL_WELCOME_CONFIRM_MAIL;
				break;
			case static::REGISTRATION_METHOD_SELF_CONFIRM | static::REGISTRATION_METHOD_MODERATOR_CONFIRM:
				$relevantEmailFlag |= static::REGISTRATION_EMAIL_CONFIRM_MAIL;
				$relevantEmailFlag |= static::REGISTRATION_EMAIL_DOUBLE_CONFIRM_MAIL;
				break;
			case static::REGISTRATION_METHOD_AUTO_ENABLE | static::REGISTRATION_METHOD_SELF_CONFIRM:
				$relevantEmailFlag |= static::REGISTRATION_EMAIL_WELCOME_CONFIRM_MAIL;
				break;
		}
		
		for($i = 0; (1 << $i) <= $relevantEmailFlag; $i++){
			if(!($emailFlag & (1 << $i))){
				$relevantEmailFlag &= ~(1 << $i);
			}
		}
		
		return $relevantEmailFlag;
	}
}
