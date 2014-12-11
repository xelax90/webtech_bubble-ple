<?php
/* 
 * Copyright (C) 2014 schurix
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

namespace SkelletonApplication\Options;

use Zend\Stdlib\AbstractOptions;
 
class SkelletonOptions extends AbstractOptions
{
	protected $guestRole = 'guest';
	protected $userRole = 'user';
	protected $moderatorRole = 'moderator';
	protected $adminRole = 'administrator';
	
	public function getGuestRole(){
		return $this->guestRole;
	}

	public function setGuestRole($guestRole){
		$this->guestRole = $guestRole;
	}

	public function getUserRole(){
		return $this->userRole;
	}

	public function setUserRole($userRole){
		$this->userRole = $userRole;
	}

	public function getModeratorRole(){
		return $this->moderatorRole;
	}

	public function setModeratorRole($moderatorRole){
		$this->moderatorRole = $moderatorRole;
	}

	public function getAdminRole(){
		return $this->adminRole;
	}

	public function setAdminRole($adminRole){
		$this->adminRole = $adminRole;
	}
}