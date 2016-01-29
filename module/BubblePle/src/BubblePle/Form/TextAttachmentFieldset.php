<?php
namespace BubblePle\Form;

use BubblePle\Entity\TextAttachment;

/**
 * TextAttachment Fieldset
 */
class TextAttachmentFieldset extends AttachmentFieldset{
	
	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'TextAttachmentFieldset';
		}
		parent::__construct($name, $options);
	}

	public function init(){
		parent::init();
		$this->setObject(new TextAttachment());


		$this->add(array(
			'name' => 'content',
			'type' => 'Textarea',
			'options' => array(
				'label' => gettext_noop('Content'),
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
			'content' => array(
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
