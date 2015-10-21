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
use DoctrineModule\Persistence\ProvidesObjectManager;

use SkelletonApplication\Entity\Role;

/**
 * RoleFieldset Fieldset
 *
 * @author schurix
 */
class RoleFieldset extends Fieldset implements InputFilterProviderInterface, ObjectManagerAwareInterface{
	use ProvidesObjectManager;
	
	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'RoleFieldset';
		}
		parent::__construct($name, $options);
	}
	
	public function init(){
		$this->setHydrator(new DoctrineHydrator($this->getObjectManager()))
			 ->setObject(new Role());
		
		$this->add(array(
			'name' => 'roleId',
			'type' => 'Text',
			'options' => array(
				'label' => gettext_noop('Role Id'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
			)
		));
		
		$this->add(array(
			'name' => 'parent',
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'options' => array(
				'object_manager' => $this->getObjectManager(),
				'target_class' => Role::class,
				'label_generator' => function($item) {
					return $item->getRoleId();
				},
				'display_empty_item' => true,
				'empty_item_label' => gettext_noop('Root'),
				'label' => gettext_noop('Parent'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
			)
		));
	}
	
	public function getInputFilterSpecification() {
		$filters = array(
			'id' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'Int'),
				),
			),
			'parent' => array(
				'required' => false,
			)
		);
		return $filters;
	}
}
