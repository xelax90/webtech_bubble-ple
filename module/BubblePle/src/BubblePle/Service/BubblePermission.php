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

namespace BubblePle\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use BubblePle\Entity\Bubble;
use BjyAuthorize\Service\Authorize;

/**
 * Description of BubblePermission
 *
 * @author schurix
 */
class BubblePermission implements ServiceLocatorAwareInterface{
	use ServiceLocatorAwareTrait;
	
	protected $auth;
	
	/**
	 * @var Authorize
	 */
	protected $allowedService;
	
	/**
	 * @return \Zend\Authentication\AuthenticationService
	 */
	public function getAuthService(){
		if(null === $this->auth){
			$this->auth = $this->getServiceLocator()->get('zfcuser_auth_service');
		}
		return $this->auth;
	}
	
	protected function isAllowed($resource, $privilege){
		if(null === $this->allowedService){
			$this->allowedService = $this->getServiceLocator()->get(Authorize::class);
		}
		return $this->allowedService->isAllowed($resource, $privilege);
	}
	
	public function isOwner(Bubble $bubble){
		if(!$this->getAuthService()->hasIdentity()){
			return false;
		}
		
		return $bubble->getOwner() == $this->getAuthService()->getIdentity();
	}
	
	public function isSharedWith(Bubble $bubble = null, $depth = 0){
		if($depth > 20){
			return false;
		}
		
		if(!$bubble){
			return true;
		}
		
		if(!$this->getAuthService()->hasIdentity()){
			return false;
		}
		
		if(empty($bubble->getShares())){
			return false;
		}
		
		foreach($bubble->getShares() as $share){
			/* @var $share \BubblePle\Entity\BubbleShare */
			if($share->getSharedWith() == $this->getAuthService()->getIdentity()){
				return true;
			}
		}
		
		$parents = $bubble->getParents();
		foreach($parents as $parent){
			if($this->isSharedWith($parent->getFrom(), $depth+1)){
				return true;
			}
		}
		return false;
	}
	
	protected function hasAccess($accessType, Bubble $bubble = null, $allowShare = false){
		if(!$bubble){
			return true;
		}
		
		// Admins can do everything
		if($this->isAllowed('bubble', $accessType)){
			return true;
		}
		
		if($this->isOwner($bubble)){
			return true;
		}
		
		if($allowShare && $this->isSharedWith($bubble)){
			return true;
		}
		
		return false;
	}
	
	public function canEdit(Bubble $bubble = null){
		return $this->hasAccess('edit', $bubble);
	}
	
	public function canDelete(Bubble $bubble = null){
		return $this->hasAccess('delete', $bubble);
	}
	
	public function canView(Bubble $bubble = null){
		return $this->hasAccess('view', $bubble, true);
	}
	
	public function canShare(Bubble $bubble = null){
		return $this->canEdit($bubble);
	}
}
