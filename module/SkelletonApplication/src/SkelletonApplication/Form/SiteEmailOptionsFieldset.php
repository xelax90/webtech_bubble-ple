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

/**
 * SiteEmailOptionsFieldset Fieldset
 *
 * @author schurix
 */
class SiteEmailOptionsFieldset extends Fieldset implements InputFilterProviderInterface{
	
	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'SiteEmailOptionsFieldset';
		}
		parent::__construct($name, $options);
	}
	
	public function init(){
		
		$this->add(array(
			'name' => 'subject',
			'type' => 'Text',
			'options' => array(
				'label' => gettext_noop('Subject'),
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
			'name' => 'template',
			'type' => 'Textarea',
			'options' => array(
				'label' => gettext_noop('Template'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
				'value_use_pre' => true,
			),
			'attributes' => array(
				'id' => "",
			)
		));
	}
	
	public function getInputFilterSpecification() {
		$filters = array(
			'subject' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'StringTrim'),
				),
			),
			'template' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'StringTrim'),
				),
			),
		);
		return $filters;
	}
}
