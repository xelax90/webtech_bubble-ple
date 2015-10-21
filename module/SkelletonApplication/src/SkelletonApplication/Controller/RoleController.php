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

namespace SkelletonApplication\Controller;

use XelaxTreeEditor\Controller\TreeEditorController;
use SkelletonApplication\Entity\Role;

/**
 * Role controller
 *
 * @author schurix
 */
class RoleController extends TreeEditorController{
	
	const ROLE_ADMIN = 'administrator';
	const ROLE_USER = 'user';
	
	public function _preDelete($item) {
		if($item->getRoleId() === static::ROLE_ADMIN){
			return false;
		}
		if($item->getRoleId() === static::ROLE_USER){
			return false;
		}
		return true;
	}
	
	protected function _editItem($item, $form, $data = null) {
		$em = $this->getEntityManager();
		$form->setBindOnValidate(false);
		$form->bind($item);
		
        /** @var $request \Zend\Http\Request */
        $request = $this->getRequest();
		if ($request->isPost() || ($request->isPut() && $data !== null)) {
			if($data === null){
				$data = array_merge_recursive(
					$request->getPost()->toArray(),
					$request->getFiles()->toArray()
				);
			}
			$form->setData($data);
			if ($form->isValid()) {
				$oldId = $item->getRoleId();
				$oldParent = $item->getParent();
				$form->bindValues();
				// prevent id change for administrator
				if(in_array($oldId, array(static::ROLE_ADMIN, static::ROLE_USER)) && $item->getRoleId() != $oldId){
					$this->flashMessenger()->addErrorMessage($this->getTranslator()->translate('You cannot change the administrator or user role id'));
					$item->setRoleId($oldId);
				}
				
				$i = 0;
				$cycle = false;
				$curr = $item->getParent();
				while($curr){
					if($curr->getRoleId() === static::ROLE_USER){
						$userFound = true;
					}
					$curr = $curr->getParent();
					$i++;
					if($i > 10){
						$this->flashMessenger()->addErrorMessage($this->getTranslator()->translate('Cycle detected'));
						$item->setParent($oldParent);
						$cycle = true;
						break;
					}
				}
				
				if(!$cycle){
					$adminRole = $em->getRepository(Role::class)->findOneBy(array('roleId' => static::ROLE_ADMIN));
					$userFound = false;
					$curr = $adminRole->getParent();
					while($curr){
						if($curr->getRoleId() === static::ROLE_USER){
							$userFound = true;
						}
						$curr = $curr->getParent();
					}
					if(!$userFound){
						$this->flashMessenger()->addErrorMessage($this->getTranslator()->translate('Administrator must be child of user'));
						$item->setParent($oldParent);
					}
				}
				
				$this->_preUpdate($item);
				$em->flush();
				$this->_postUpdate($item);
				return true;
			}
        }
		return false;
	}
}
