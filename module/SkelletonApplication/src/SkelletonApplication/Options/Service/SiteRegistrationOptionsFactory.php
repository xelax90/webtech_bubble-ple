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

use SkelletonApplication\Options\SiteRegistrationOptions;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use XelaxSiteConfig\Options\Service\SiteConfigService;

/**
 * Description of SiteRegistrationOptionsFactory
 *
 * @author schurix
 */
class SiteRegistrationOptionsFactory implements FactoryInterface {
	const CONFIG_PREFIX = 'skelleton_application.registration';
	
    public function createService(ServiceLocatorInterface $serviceLocator) {
		/* @var $siteConfigService SiteConfigService */
		$siteConfigService = $serviceLocator->get(SiteConfigService::class);
		$config = $siteConfigService->getConfig(static::CONFIG_PREFIX);
        return new SiteRegistrationOptions($config);
    }
}
