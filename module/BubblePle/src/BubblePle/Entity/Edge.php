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
 * Edge Entity
 *
 * @ORM\Entity(repositoryClass="BubblePle\Model\EdgeRepository")
 * @ORM\Table(name="edge")
 */
class Edge implements JsonSerializable{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Bubble", inversedBy="parents")
	 * @ORM\JoinColumn(name="to_id", referencedColumnName="id")
	 */
	protected $to;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Bubble", inversedBy="children")
	 * @ORM\JoinColumn(name="from_id", referencedColumnName="id")
	 */
	protected $from;
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @param int $id
	 * @return Edge
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	
	/**
	 * @return Bubble
	 */
	public function getTo() {
		return $this->to;
	}
	
	/**
	 * @return Bubble
	 */
	public function getFrom() {
		return $this->from;
	}

	public function setTo($to) {
		$this->to = $to;
		return $this;
	}

	public function setFrom($from) {
		$this->from = $from;
		return $this;
	}
	
	public function getFromTitle(){
		if($this->getFrom()){
			return $this->getFrom()->getTitle();
		}
		return '';
	}
	
	public function getToTitle(){
		if($this->getTo()){
			return $this->getTo()->getTitle();
		}
		return '';
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
			'from' => $this->getFrom()->getId(),
			'to' => $this->getTo()->getId(),
		);
	}

}
