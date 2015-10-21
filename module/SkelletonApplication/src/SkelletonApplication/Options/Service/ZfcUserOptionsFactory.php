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

namespace SkelletonApplication\Options\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use SkelletonApplication\Options\SiteRegistrationOptions;
use SkelletonApplication\Entity\User;
use SkelletonApplication\Options\ZfcUserModuleOptions as ModuleOptions;
use Exception;

/**
 * Description of ZfcUserOptionsFactory
 *
 * @author schurix
 */
class ZfcUserOptionsFactory implements FactoryInterface{
	
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$config = $serviceLocator->get('Config');
		$siteConfig = array();
		$methodFlag = 0;
		try{
			/* @var $siteOptions SiteRegistrationOptions */
			$siteOptions = $serviceLocator->get(SiteRegistrationOptions::class);
			$methodFlag = $siteOptions->getRegistrationMethodFlag();
		} catch (Exception $ex) {}
		
		if($methodFlag & SiteRegistrationOptions::REGISTRATION_METHOD_AUTO_ENABLE){
			$siteConfig['default_user_state'] = 1 << User::STATE_ACTIVE_BIT;
			$siteConfig['login_after_registration'] = true;
		} else {
			$siteConfig['default_user_state'] = 0;
			$siteConfig['login_after_registration'] = false;
		}
		
		if($methodFlag == 0){
			$siteConfig['enable_registration'] = false;
		} else {
			$siteConfig['enable_registration'] = true;
		}
		
		$merged = $siteConfig;
		if(isset($config['zfcuser'])){
			$merged = array_merge($config['zfcuser'], $merged);
		}
		
		return new ModuleOptions($merged);
	}
}
