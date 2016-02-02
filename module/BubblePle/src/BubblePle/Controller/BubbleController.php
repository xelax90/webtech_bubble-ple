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

namespace BubblePle\Controller;

use XelaxAdmin\Controller\ListController;

/**
 * Controller that handles bubbles
 *
 * @author schurix
 */
class BubbleController extends ListController{
	
	/**
	 * Returns list of all items to show in list view. Overwrite to add custom filters
	 * @return \Traversable
	 */
	protected function getAll(){
		if(!$this->zfcUserAuthentication()->hasIdentity()){
			return array();
		}
		
		$em = $this->getEntityManager();
		$entityClass = $this->getEntityClass();
		
		$user = $this->zfcUserAuthentication()->getIdentity();
		
		$params = array(
			'owner' => $user,
		);
		
		if(!empty($this->getParentControllerOptions())){
			$parentId = $this->getEvent()->getRouteMatch()->getParam($this->getParentControllerOptions()->getIdParamName());
			$params[$this->getOptions()->getParentAttributeName()] = $parentId;
		}
		
		$items = $em->getRepository($entityClass)->findBy($params);
		
		return $items;
	}
	
	protected function _preCreate($item) {
		parent::_preCreate($item);
		$user = $this->zfcUserAuthentication()->getIdentity();
		$item->setOwner($user);
	}
	
	protected function canEdit($item){
		/* @var $item \BubblePle\Entity\Bubble */
		$isAdmin = call_user_func($this->plugin('isAllowed'), 'bubble', 'edit');
		if($isAdmin){
			return true;
		}
		
		if(!$this->zfcUserAuthentication()->hasIdentity()){
			return false;
		}
		
		if($this->zfcUserAuthentication()->getIdentity() === $item->getOwner()){
			return true;
		}
		
		return false;
	}
	
	protected function canDelete($item){
		/* @var $item \BubblePle\Entity\Bubble */
		$isAdmin = call_user_func($this->plugin('isAllowed'), 'bubble', 'delete');
		if($isAdmin){
			return true;
		}
		
		if(!$this->zfcUserAuthentication()->hasIdentity()){
			return false;
		}
		
		if($this->zfcUserAuthentication()->getIdentity() === $item->getOwner()){
			return true;
		}
		
		return false;
	}
	
	protected function _editItem($item, $form, $data = null) {
		if(!$this->canEdit($item)){
			$this->flashMessenger()->addErrorMessage($this->getTranslator()->translate('Not authorized'));
			return false;
		}
		return parent::_editItem($item, $form, $data);
	}
	
	protected function _delteItem($item) {
		if(!$this->canDelete($item)){
			$this->flashMessenger()->addErrorMessage($this->getTranslator()->translate('Not authorized'));
			return false;
		}
		return parent::_delteItem($item);
	}
	
}
