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

namespace SkelletonApplication\Authentication\Storage;

use ZfcUser\Authentication\Storage\Db as ZfcDb;
use Zend\Authentication\Storage;


/**
 * Extends Zfc Db storage by isActive check
 *
 * @author schurix
 */
class Db extends ZfcDb{
	
	public function read() {
		$identity = parent::read();
		/* @var $config \SkelletonApplication\Options\ZfcUserModuleOptions */
		$config = $this->getServiceManager()->get('zfcuser_module_options');
		// If userState is enabled, check if user is allowed to log in
		if($config->getEnableUserState() && !in_array($identity->getState(), $config->getAllowedLoginStates())){
			$this->clear();
			return $this->resolvedIdentity;
		}
		return $identity;
	}
	
	public function getStorage() {
        if (null === $this->storage) {
			/* @var $config \SkelletonApplication\Options\ZfcUserModuleOptions */
			$config = $this->getServiceManager()->get('zfcuser_module_options');
			// use sessionNamespace parameter if present
			$namespace = null;
			if(is_callable(array($config, 'getSessionNamespace'))){
				$namespace = $config->getSessionNamespace();
			}
            $this->setStorage(new Storage\Session($namespace));
        }
        return $this->storage;
	}
	
}
