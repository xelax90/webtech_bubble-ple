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
 * Translation Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="translation", uniqueConstraints={@ORM\UniqueConstraint(name="translation_uniq", columns={"translationKey", "locale", "textDomain"})}, indexes={@ORM\Index(name="locale_domain_idx", columns={"locale", "textDomain"})})
 */
class Translation implements JsonSerializable{
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string", nullable=false)
	 */
	protected $translationKey;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $textDomain = 'default';
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $locale;
	
	/**
	 * @ORM\Column(type="text")
	 */
	protected $translation;
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @param int $id
	 * @return Translation
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}	
	
	public function getTranslationKey() {
		return $this->translationKey;
	}

	public function getLocale() {
		return $this->locale;
	}

	public function getTranslation() {
		return $this->translation;
	}

	public function setTranslationKey($translationKey) {
		$this->translationKey = $translationKey;
		return $this;
	}

	public function setLocale($locale) {
		$this->locale = $locale;
		return $this;
	}

	public function setTranslation($translation) {
		$this->translation = $translation;
		return $this;
	}
	
	public function getTextDomain() {
		return $this->textDomain;
	}

	public function setTextDomain($textDomain) {
		$this->textDomain = $textDomain;
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
			'translationKey' => $this->getTranslationKey(),
			'locale' => $this->getLocale(),
			'translation' => $this->getTranslation(),
		);
	}

}
