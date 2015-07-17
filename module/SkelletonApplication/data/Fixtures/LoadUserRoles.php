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
use \Doctrine\Common\DataFixtures\AbstractFixture;

class LoadUserRoles extends AbstractFixture implements FixtureInterface, ServiceLocatorAwareInterface
{
	/**
	 *
	 * @var ServiceLocatorInterface
	 */
	protected $sl;
	
	/**
	 *
	 * @var \SkelletonApplication\Options\SkelletonOptions
	 */
	protected $skelletonOptions;
	
    /**
     * @return \SkelletonApplication\Options\SkelletonOptions
     */
    public function getSkelletonOptions()
    {
        if (!$this->skelletonOptions instanceof \SkelletonApplication\Options\SkelletonOptions) {
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
		$config = $this->getServiceLocator()->get('config');
		$roleEntity = $config['zfcuser']['role_entity_class'];
		$repo = $manager->getRepository($roleEntity);
		foreach($roles as $roleName => $children){
			$found = $repo->findOneByRoleId($roleName);
			if($found){
				if(empty($children) && strpos(strtolower($roleName), 'admin') !== false){
					$this->addReference('admin-role', $found);
				}
				continue;
			}
			
			$role = new $roleEntity();
			$role->setRoleId($roleName);
			$role->setParent($parent);
			$manager->persist($role);
			$this->addReference('role/'.$roleName, $role);
			$this->saveRoles($manager, $children, $role);
			
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

}