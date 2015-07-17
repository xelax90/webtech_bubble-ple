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

/**
 * UserProfile Entity
 *
 * @author schurix
 * 
 * @ORM\Entity
 * @ORM\Table(name="userprofile")
 */
class UserProfile implements JsonSerializable{
	/**
	 * @ORM\Id
	 * @ORM\OneToOne(targetEntity="SkelletonApplication\Entity\User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
	 */
	protected $user;
	
	/**
	 * @return User
	 */
	public function getUser() {
		return $this->user;
	}
	
	/**
	 * @param User $user
	 * @return UserProfile
	 */
	public function setUser($user) {
		$this->user = $user;
		return $this;
	}
	
	/**
	 * Returns the user id
	 * @return int
	 */
	public function getUserId() {
		return $this->getUser()->getId();
	}
	
	/**
	 * Returns the user displayName
	 * @return string
	 */
	public function getDisplayName(){
		return $this->getUser()->getDisplayName();
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
			'user_id' => $this->getUser()->getId(),
		);
	}

}
