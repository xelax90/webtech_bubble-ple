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
		);
		$filters = array_merge(parent::getInputFilterSpecification(), $filters);
		return $filters;
	}
}
