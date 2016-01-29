<?php
namespace BubblePle\Form;

use BubblePle\Entity\ImageAttachment;

/**
 * ImageAttachment Fieldset
 */
class ImageAttachmentFieldset extends MediaAttachmentFieldset{

	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'ImageAttachmentFieldset';
		}
		parent::__construct($name, $options);
	}

	public function init(){
		parent::init();
		$this->setObject(new ImageAttachment());

	}

	public function getInputFilterSpecification() {
		$filters = array(
			'filename' => array(
				'filters' => array(
					array(
						'options' => array(
							'target' => 'public/files/imageattachment/',
						),
					),
				),
				'validators' => array(
					array('name' => 'Zend\Validator\File\Extension', 'options' => array('extension' => array('gif', 'png', 'jpg', 'jpeg'))),
					array('name' => 'Zend\Validator\File\IsImage')
				),
			),
		);
		$filters = array_merge(parent::getInputFilterSpecification(), $filters);
		return $filters;
	}
}
