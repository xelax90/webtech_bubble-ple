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
 * Link Attachment
 *
 * @ORM\Entity(repositoryClass="BubblePle\Model\BubbleRepository")
 */
class LinkAttachment extends Attachment{
	
	/**
	 * @ORM\Column(type="text")
	 */
	protected $url;
	
	public function getUrl() {
		return $this->url;
	}

	public function setUrl($url) {
		$this->url = $url;
		return $this;
	}

	/**
	 * Returns data to show in json
	 * @return array
	 */
	public function jsonSerialize() {
		$data = parent::jsonSerialize();
		return array_merge($data, array(
			'url' => $this->getUrl(),
		));
	}
	
}
