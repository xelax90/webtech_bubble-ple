<?php
namespace BubblePle\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;

use BubblePle\Entity\Edge;
use BubblePle\Entity\Bubble;
use DoctrineModule\Form\Element\ObjectSelect;

/**
 * Edge Fieldset
 */
class EdgeFieldset extends Fieldset implements InputFilterProviderInterface, ObjectManagerAwareInterface{
	use ProvidesObjectManager;

	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'EdgeFieldset';
		}
		parent::__construct($name, $options);
	}

	public function init(){
		parent::init();
		$this->setHydrator(new DoctrineHydrator($this->getObjectManager()))
			 ->setObject(new Edge());
		
		$this->add(array(
			'name' => 'from',
			'type' => ObjectSelect::class,
			'options' => array(
				'object_manager' => $this->getObjectManager(),
				'target_class' => Bubble::class,
				'label_generator' => function($item) {
					$o = $item->getOwner();
					return $item->getTitle() . ' ('.($o ? $o->getDisplayName() : '').')';
				},
				'display_empty_item' => true,
				'empty_item_label' => gettext_noop('-- Bubble From --'),
				'label' => gettext_noop('Bubble From'),
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
			'name' => 'to',
			'type' => ObjectSelect::class,
			'options' => array(
				'object_manager' => $this->getObjectManager(),
				'target_class' => Bubble::class,
				'label_generator' => function($item) {
					$o = $item->getOwner();
					return '('.$item->getId().')'.$item->getTitle() . ' ('.($o ? $o->getDisplayName() : '').')';
				},
				'display_empty_item' => true,
				'empty_item_label' => gettext_noop('-- Bubble to --'),
				'label' => gettext_noop('Bubble to'),
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
			'from' => array(
				'required' => true,
			),
			'to' => array(
				'required' => true,
			),
		);
		return $filters;
	}
}
