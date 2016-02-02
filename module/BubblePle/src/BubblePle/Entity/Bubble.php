<?php
/* 
 * Copyright (C) 2016 schurix
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace BubblePle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;

/**
 * Bubble Entity
 *
 * @ORM\Entity(repositoryClass="BubblePle\Model\BubbleRepository")
 * @ORM\Table(name="bubble")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 */
class Bubble implements JsonSerializable{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $title;
	
	/**
	 * @ORM\ManyToOne(targetEntity="SkelletonApplication\Entity\User")
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="user_id")
	 */
	protected $owner;
	
	/**
	 * @ORM\OneToMany(targetEntity="Edge", mappedBy="from")
	 */
	protected $children;
	
	/**
	 * @ORM\OneToMany(targetEntity="Edge", mappedBy="to")
	 */
	protected $parents;
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @param int $id
	 * @return Bubble
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	
	function getTitle() {
		return $this->title;
	}

	function setTitle($title) {
		$this->title = $title;
		return $this;
	}

	public function getOwner() {
		return $this->owner;
	}

	public function setOwner($owner) {
		$this->owner = $owner;
		return $this;
	}
	
	public function getChildren() {
		return $this->children;
	}

	public function getParents() {
		return $this->parents;
	}

	public function setChildren($children) {
		$this->children = $children;
		return $this;
	}

	public function setParents($parents) {
		$this->parents = $parents;
		return $this;
	}

	/**
	 * Returns json String
	 * @return string
	 */
	public function toJson(){
		$data = $this->jsonSerialize();
		return Json::encode($data, true, array('silenceCyclicalExceptions' => true));
	}
	
	/**
	 * Returns data to show in json
	 * @return array
	 */
	public function jsonSerialize() {
		return array(
			'bubbleType' => get_class($this),
			'id' => $this->getId(),
			'title' => $this->getTitle(),
		);
	}
	
	/**
	 * Returns a unique string for each entity. Allows to use array_intersect 
	 * for arrays of Bubbles
	 * @return string
	 */
	public function __toString() {
		return (string) $this->getId();
	}
}
