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

namespace SkelletonApplication\Fixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use SkelletonApplication\Entity\User;
use Zend\Crypt\Password\Bcrypt;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;

class LoadAdminUser extends AbstractFixture implements FixtureInterface, ServiceLocatorAwareInterface, DependentFixtureInterface
{
	/**
	 *
	 * @var ServiceLocatorInterface
	 */
	protected $sl;
	
	/**
	 *
	 * @var \ZfcUser\Options\ModuleOptions
	 */
	protected $zfcUserOptions;
	
    /**
     * @return \ZfcUser\Options\ModuleOptions
     */
    public function getZfcUserOptions()
    {
        if (!$this->zfcUserOptions instanceof ZfcUserModuleOptions) {
            $this->zfcUserOptions = $this->getServiceLocator()->get('zfcuser_module_options');
        }
        return $this->zfcUserOptions;
    }
	
	
    public function load(ObjectManager $manager)
    {
		$userService = $this->getServiceLocator()->get('zfcuser_user_service');
		
		$data = array(
			'username' => 'xelax90',
			'display_name' => 'Xelax 90',
			'email' => 'schurix@gmx.de',
			'password' => 'schurix',
			'passwordVerify' => 'schurix'
		);
		/* @var $userObject User */
		$userObject = $userService->register($data);
		if(!$userObject){
			throw new Exception(sprintf('Registration of user %s failed', $item->name));
		}
		$userObject->setUsername($data['username']);
		$userObject->setEmail($data['email']);
		$userObject->setState(1);
		$userObject->addRoles(array($this->getReference('admin-role')));
        $manager->flush();
		
		$this->addReference('admin-user', $userObject);
    }

	/**
	 * Returns ServiceLocator
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator() {
		return $this->sl;
	}
	
	/**
	 * Sets ServiceLocator
	 * @param ServiceLocatorInterface $serviceLocator
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
		$this->sl = $serviceLocator;
	}

	public function getDependencies() {
		return array('SkelletonApplication\Fixtures\LoadUserRoles');
	}

}