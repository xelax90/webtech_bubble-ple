<?php
namespace BubblePle\Form;

use BubblePle\Entity\MediaAttachment;

/**
 * MediaAttachment Fieldset
 */
class MediaAttachmentFieldset extends FileAttachmentFieldset {

	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'MediaAttachmentFieldset';
		}
		parent::__construct($name, $options);
	}

	public function init(){
		parent::init();
		$this->setObject(new MediaAttachment());

	}

	public function getInputFilterSpecification() {
		$filters = array(
			'filename' => array(
				'filters' => array(
					'rename' => array(
						'options' => array(
							'target' => 'public/files/mediaattachment/',
						),
					),
				),
			),
		);
		$filters = array_replace_recursive(parent::getInputFilterSpecification(), $filters);
		return $filters;
	}
}
