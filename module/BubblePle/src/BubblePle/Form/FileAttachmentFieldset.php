<?php
namespace BubblePle\Form;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

use BubblePle\Entity\FileAttachment;

/**
 * FileAttachment Fieldset
 */
class FileAttachmentFieldset extends AttachmentFieldset{

	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'FileAttachmentFieldset';
		}
		parent::__construct($name, $options);
	}

	public function init(){
		parent::init();
		$this->setHydrator(new DoctrineHydrator($this->getObjectManager()))
			 ->setObject(new FileAttachment());


		$this->add(array(
			'name' => 'filename',
			'type' => 'File',
			'options' => array(
				'label' => gettext_noop('Filename'),
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
			'filename' => array(
				"type" => "Zend\InputFilter\FileInput",
				'required' => true,
				'filters' => array(
					array(
						'name' => 'Zend\Filter\File\RenameUpload',
						'options' => array(
							'target' => 'public/files/fileattachment/',
							'randomize' => true,
							'use_upload_extension' => true,
							'use_upload_name' => true
						),
					),
				),
				'validators' => array(
				),
			),
		);
		$filters = array_merge(parent::getInputFilterSpecification(), $filters);
		return $filters;
	}
}
