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
 * Fieldset for SMTP transport configuration
 *
 * @author schurix
 */
class SmtpOptionsFieldset extends Fieldset implements InputFilterProviderInterface{
	
	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'SmtpOptionsFieldset';
		}
		parent::__construct($name, $options);
	}
	
	public function init(){
		
		$this->add(array(
			'name' => 'host',
			'type' => 'Text',
			'options' => array(
				'label' => gettext_noop('Host'),
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
			'name' => 'port',
			'type' => 'Number',
			'options' => array(
				'label' => gettext_noop('Port'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
				'min' => 0,
				'max' => pow(2,16) - 1,
				'step' => 1,
			)
		));
		
		$this->add(array(
            'name' => 'connection_class',
            'type' => 'select',
            'options' => array(
                'label' => gettext_noop('Connection class'),
                'value_options' => array(
					'smtp'    => 'SMTP',
					'plain'   => 'Plain',
					'login'   => 'Login',
					'crammd5' => 'CRAM-MD5',
				),
            )
        ));
		
		$this->add(array(
			'name' => 'username',
			'type' => 'Text',
			'options' => array(
				'label' => gettext_noop('Username'),
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
			'name' => 'password',
			'type' => 'Password',
			'options' => array(
				'label' => gettext_noop('Password'),
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
            'name' => 'ssl',
            'type' => 'select',
            'options' => array(
                'label' => gettext_noop('SSL'),
                'value_options' => array(
					'tls'  => 'TLS',
					'ssl'  => 'SSL',
					''     => gettext_noop('None'),
				),
            ),
        ));
		
	}
	
	public function getInputFilterSpecification() {
		$filters = array(
			'host' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => 'XelaxHTMLPurifier\Filter\HTMLPurifier'),
				),
			),
			'port' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'Int'),
				),
			),
			'connection_class' => array(
				'required' => false,
			),
			'username' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => 'XelaxHTMLPurifier\Filter\HTMLPurifier'),
				),
			),
			'password' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => 'XelaxHTMLPurifier\Filter\HTMLPurifier'),
				),
			),
			'ssl' => array(
				'required' => false,
			),
		);
		return $filters;
	}
}
