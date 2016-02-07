<?php
namespace BubblePle\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;

use BubblePle\Entity\Bubble;

/**
 * Bubble Fieldset
 */
class BubbleFieldset extends Fieldset implements InputFilterProviderInterface, ObjectManagerAwareInterface{
	use ProvidesObjectManager;

	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'BubbleFieldset';
		}
		parent::__construct($name, $options);
	}

	public function init(){
		parent::init();
		$this->setHydrator(new DoctrineHydrator($this->getObjectManager()))
			 ->setObject(new Bubble());


		$this->add(array(
			'name' => 'title',
			'type' => 'Text',
			'options' => array(
				'label' => gettext_noop('Title'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
			)
		));

		$this->add(array(
			'name' => 'posX',
			'type' => 'Number',
			'options' => array(
				'label' => gettext_noop('Pos X'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
				'step' => '1',
			)
		));
		
		$this->add(array(
			'name' => 'posY',
			'type' => 'Number',
			'options' => array(
				'label' => gettext_noop('Pos Y'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
				'step' => '1',
			)
		));
		
		$this->add(array(
			'name' => 'priority',
			'type' => 'Text',
			'options' => array(
				'label' => gettext_noop('Priority'),
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

			'title' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => 'XelaxHTMLPurifier\Filter\HTMLPurifier'),
				),
			),
			
			'posX' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'Int'),
				),
			),
			
			'posY' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'Int'),
				),
			),
			
			'priority' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => 'XelaxHTMLPurifier\Filter\HTMLPurifier'),
				),
			),
		);
		return $filters;
	}
}
