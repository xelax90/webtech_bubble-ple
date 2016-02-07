<?php
namespace BubblePle\Form;

use BubblePle\Entity\Semester;

/**
 * Semester Fieldset
 */
class SemesterFieldset extends BubbleFieldset{

	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'SemesterFieldset';
		}
		parent::__construct($name, $options);
	}

	public function init(){
		parent::init();
		$this->setObject(new Semester());
		
		$this->remove('title');

		$this->add(array(
			'name' => 'year',
			'type' => 'Number',
			'options' => array(
				'label' => gettext('Year'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
				'min' => '0',
				'step' => '1'
			)
		));


		$this->add(array(
			'name' => 'isWinter',
			'type' => 'Checkbox',
			'options' => array(
				'label' => '',
				'use-switch' => true,
				'checked_value' => '1',
				'label_options' => array(
					'position' => \Zend\Form\View\Helper\FormRow::LABEL_PREPEND,
				),
				'column-size' => 'sm-10 col-sm-offset-2',
			),
			'attributes' => array(
				'id' => "",
				'data-label-text' => gettext_noop('Term'),
				'data-label-width' => '100',
				'data-off-color' => 'warning',
				'data-on-text' => gettext_noop('Winter'),
				'data-off-text' => gettext_noop('Summer'),
			)
		));

	}

	public function getInputFilterSpecification() {
		$filters = array(
			'year' => array(
				'required' => true,
			),

			'isWinter' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'Digits'),
				),
			),
			
			'title' => array(
				'required' => false,
			),
		);
		$filters = array_replace_recursive(parent::getInputFilterSpecification(), $filters);
		return $filters;
	}
}
