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

use GoalioMailService\Mail\Options\TransportOptions;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\Mail\Transport\Smtp;

class TransportOptionsFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {
		/* @var $config \SkelletonApplication\Options\SiteEmailOptions */
        $config = $serviceLocator->get('SkelletonApplication\Options\Site\Email');
		
		$transportConfig = array(
			'type' => $config->getType(),
		);
		
		if($config->getType() === Smtp::class){
			$transportConfig['options'] = $config->getSmtpOptions();
		}
        return new TransportOptions($transportConfig);
    }

}
