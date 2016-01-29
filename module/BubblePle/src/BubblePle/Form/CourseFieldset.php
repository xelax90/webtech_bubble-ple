<?php
namespace BubblePle\Form;

use BubblePle\Entity\Course;

/**
 * Course Fieldset
 */
class CourseFieldset extends BubbleFieldset{

	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'CourseFieldset';
		}
		parent::__construct($name, $options);
	}

	public function init(){
		parent::init();
		$this->setObject(new Course());


		$this->add(array(
			'name' => 'courseroom',
			'type' => 'Text',
			'options' => array(
				'label' => gettext_noop('L2P courseroom'),
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
			'courseroom' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => 'XelaxHTMLPurifier\Filter\HTMLPurifier'),
				),
			),

		);
		$filters = array_merge(parent::getInputFilterSpecification(), $filters);
		return $filters;
	}
}
