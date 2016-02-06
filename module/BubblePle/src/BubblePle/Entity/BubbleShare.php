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
use SkelletonApplication\Entity\User;

/**
 * BubbleShare Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="bubbleshare")
 */
class BubbleShare implements JsonSerializable{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Bubble", inversedBy="shares")
	 * @ORM\JoinColumn(name="bubble_id", referencedColumnName="id")
	 */
	protected $bubble;
	
	/**
	 * @ORM\ManyToOne(targetEntity="SkelletonApplication\Entity\User")
	 * @ORM\JoinColumn(name="sharedWith_id", referencedColumnName="user_id")
	 */
	protected $sharedWith;
	
	/**
	 * @return Bubble
	 */
	public function getBubble() {
		return $this->bubble;
	}
	
	/**
	 * @return User
	 */
	public function getSharedWith() {
		return $this->sharedWith;
	}

	public function setBubble($bubble) {
		$this->bubble = $bubble;
		return $this;
	}

	public function setSharedWith($sharedWith) {
		$this->sharedWith = $sharedWith;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @param int $id
	 * @return BubbleShare
	 */
	public function setId($id) {
		$this->id = $id;
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
			'id' => $this->getId(),
			'bubble' => $this->getBubble()->getId(),
			'sharedWith' => $this->getSharedWith()->getId(),
		);
	}

}
