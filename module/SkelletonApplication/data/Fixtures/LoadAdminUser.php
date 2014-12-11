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

class LoadAdminUser implements FixtureInterface, ServiceLocatorAwareInterface, DependentFixtureInterface
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
        $zfcUserOptions = $this->getZfcUserOptions();
		// create user schurix@gmx.de with password schurix
        $user = new User();
		$user->setEmail('schurix@gmx.de');
		$user->setDisplayName('Xelax 90');
		$user->setUsername('xelax90');
		$user->setState(1);
		
		$role = $this->getReference('admin-role');
		$user->addRole($role);
		
        $bcrypt = new Bcrypt;
        $bcrypt->setCost($zfcUserOptions->getPasswordCost());
        $user->setPassword($bcrypt->create('schurix'));

        $manager->persist($user);
        $manager->flush();
		
		$this->addReference('admin-user', $user);
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