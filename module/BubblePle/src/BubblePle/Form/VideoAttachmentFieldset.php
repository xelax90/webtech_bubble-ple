<?php
namespace BubblePle\Form;

use BubblePle\Entity\VideoAttachment;

/**
 * VideoAttachment Fieldset
 */
class VideoAttachmentFieldset extends MediaAttachmentFieldset{
	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'VideoAttachmentFieldset';
		}
		parent::__construct($name, $options);
	}

	public function init(){
		parent::init();
		$this->setObject(new VideoAttachment());

	}

	public function getInputFilterSpecification() {
		$filters = array(
			'filename' => array(
				'filters' => array(
					'rename' => array(
						'options' => array(
							'target' => 'public/files/videoattachment/',
						),
					),
				),
			),
		);
		$filters = array_replace_recursive(parent::getInputFilterSpecification(), $filters);
		return $filters;
	}
}
