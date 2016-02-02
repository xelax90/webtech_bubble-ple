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

/**
 * Semester Bubble
 *
 * @ORM\Entity(repositoryClass="BubblePle\Model\BubbleRepository")
 */
class Semester extends Bubble{
	
	/**
	 * @ORM\Column(type="integer")
	 */
	protected $year;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $isWinter;
	
	public function getYear() {
		return $this->year;
	}

	public function getIsWinter() {
		return $this->isWinter;
	}

	public function setYear($year) {
		$this->year = $year;
		$this->updateTitle();
		return $this;
	}

	public function setIsWinter($isWinter) {
		$this->isWinter = $isWinter;
		$this->updateTitle();
		return $this;
	}
	
	public function updateTitle(){
		$winter = $this->getIsWinter() ? 'WS' : 'SS';
		$year = $this->getIsWinter() ? $this->getYear().'/'.($this->getYear()+1) : $this->getYear();
		$this->setTitle($winter.' '.$year);
	}
	

	/**
	 * Returns data to show in json
	 * @return array
	 */
	public function jsonSerialize() {
		$data = parent::jsonSerialize();
		return array_merge($data, array(
			'year' => $this->getYear(),
			'isWinter' => $this->getIsWinter(),
		));
	}
	
}
