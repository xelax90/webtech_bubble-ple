<?php
/* 
 * Copyright (C) 2015 schurix
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

namespace SkelletonApplication\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;
use Eye4web\SiteConfig\Entity\SiteConfigInterface;

/**
 * SiteConfig Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="siteconfig")
 */
class SiteConfig implements JsonSerializable, SiteConfigInterface{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string", unique=true, nullable=false)
	 */
	protected $confKey;
	
	/**
	 * @ORM\Column(type="text")
	 */
	protected $value = '';
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @param int $id
	 * @return SiteConfig
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	
	public function getKey() {
		return $this->confKey;
	}

	public function getValue() {
		return $this->value;
	}

	public function setKey($key) {
		$this->confKey = $key;
		return $this;
	}

	public function setValue($value) {
		$this->value = $value;
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
			'key' => $this->getKey(),
			'value' => $this->getValue(),
		);
	}

}
