<?php
namespace BubblePle\Form;

use BubblePle\Entity\LinkAttachment;

/**
 * LinkAttachment Fieldset
 */
class LinkAttachmentFieldset extends AttachmentFieldset{

	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'LinkAttachmentFieldset';
		}
		parent::__construct($name, $options);
	}

	public function init(){
		parent::init();
		$this->setObject(new LinkAttachment());


		$this->add(array(
			'name' => 'url',
			'type' => 'Text',
			'options' => array(
				'label' => gettext_noop('URL'),
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

			'url' => array(
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
