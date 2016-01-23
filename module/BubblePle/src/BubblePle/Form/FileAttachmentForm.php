<?php
namespace BubblePle\Form;

use Zend\Form\Form;
use Zend\Form\Element\Csrf;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\InputFilter\InputFilter;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;

/**
 * FileAttachment Form
 *
 * @author schurix
 */
class FileAttachmentForm extends Form implements ObjectManagerAwareInterface{
	use ProvidesObjectManager;

	public function __construct($name = "", $options = array()){
		// we want to ignore the name passed
		parent::__construct('FileAttachmentForm', $options);
		$this->setAttribute('method', 'post');
	}

	public function init(){
		parent::init();
		$this->setHydrator(new DoctrineHydrator($this->getObjectManager()))
			 ->setInputFilter(new InputFilter());

		$this->add(array(
			'name' => 'fileattachment',
			'type' => FileAttachmentFieldset::class,
			'options' => array(
				'use_as_base_fieldset' => true,
			),
		));
		
		/*$this->add(array(
			'name' => 'fileattachment_csrf',
			'type' => Csrf::class,
		));*/

		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Save',
				'class' => 'btn-success'
			),
			'options' => array(
				'as-group' => true,
			)
		));
	}
}
