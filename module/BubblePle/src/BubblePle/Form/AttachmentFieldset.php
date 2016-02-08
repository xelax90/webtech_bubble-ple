<?php
namespace BubblePle\Form;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

use BubblePle\Entity\Attachment;

/**
 * Attachment Fieldset
 */
class AttachmentFieldset extends BubbleFieldset{

	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'AttachmentFieldset';
		}
		parent::__construct($name, $options);
	}

	public function init(){
		parent::init();
		$this->setHydrator(new DoctrineHydrator($this->getObjectManager()))
			 ->setObject(new Attachment());

	}

	public function getInputFilterSpecification() {
		$filters = array(
		);
		$filters = array_replace_recursive(parent::getInputFilterSpecification(), $filters);
		return $filters;
	}
}
