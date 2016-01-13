<?php
namespace BubblePle\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;

use BubblePle\Entity\Attachment;

/**
 * Attachment Fieldset
 */
class AttachmentFieldset extends BubbleFieldset implements InputFilterProviderInterface, ObjectManagerAwareInterface{
	use ProvidesObjectManager;

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
		$filters = array_merge(parent::getInputFilterSpecification(), $filters);
		return $filters;
	}
}
