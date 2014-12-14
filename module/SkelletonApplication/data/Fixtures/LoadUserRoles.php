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
use SkelletonApplication\Entity\Role;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LoadUserRoles implements FixtureInterface, ServiceLocatorAwareInterface, DependentFixtureInterface
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
	 *
	 * @var \SkelletonApplication\Options\SkelletonOptions
	 */
	protected $skelletonOptions;
	
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
	
    /**
     * @return \SkelletonApplication\Options\SkelletonOptions
     */
    public function getSkelletonOptions()
    {
        if (!$this->skelletonOptions instanceof ZfcUserModuleOptions) {
            $this->skelletonOptions = $this->getServiceLocator()->get('SkelletionApplication\Options\Application');
        }
        return $this->skelletonOptions;
    }
	
	
    public function load(ObjectManager $manager)
    {
		$skelletonOptions = $this->getSkelletonOptions();
		$roles = $skelletonOptions->getRoles();
		
		$this->saveRoles($manager, $roles);
        $manager->flush();
    }
	
	protected function saveRoles(ObjectManager $manager, $roles, $parent = null){
		foreach($roles as $roleName => $children){
			$role = new Role();
			$role->setRoleId($roleName);
			$role->setParent($parent);
			$manager->persist($role);
			$this->saveRoles($children, $role);
			
			if(empty($children) && strpos(strtolower($roleName), 'admin') !== false){
				$this->addReference('admin-role', $role);
			}
		}
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