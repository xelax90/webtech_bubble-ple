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

namespace SkelletonApplication\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;

use SkelletonApplication\Entity\UserProfile;

/**
 * UserProfileFieldset Fieldset
 *
 * @author schurix
 */
class UserProfileFieldset extends Fieldset implements InputFilterProviderInterface, ObjectManagerAwareInterface{
	protected $objectManager;
	
	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'UserProfileFieldset';
		}
		parent::__construct($name, $options);
	}
	
	public function init(){
		$this->setHydrator(new DoctrineHydrator($this->getObjectManager()))
			 ->setObject(new UserProfile());
	}
	
	public function getInputFilterSpecification() {
		$filters = array(
		);
		return $filters;
	}

	public function getObjectManager() {
		return $this->objectManager;
	}

	public function setObjectManager(ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
		return $this;
	}
}
