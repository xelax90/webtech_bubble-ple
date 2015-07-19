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

use XelaxAdmin\Controller\ListController;

use Zend\Stdlib\Hydrator\ClassMethods;
use ZfcUser\Mapper\UserInterface;
use ZfcUser\Options\ModuleOptions as ZfcUserModuleOptions;
use ZfcUserAdmin\Options\ModuleOptions;

/**
 * User admin controller
 * Mostly copy/paste from danielss89/zfc-user-admin
 *
 * @author schurix
 */
class UserController extends ListController{
	protected $zfcUserAdminOptions, $userMapper;
	protected $zfcUserOptions;
	/**
	 * @var \ZfcUserAdmin\Service\User
	 */
	protected $adminUserService;

	protected function getAll() {
        $userMapper = $this->getUserMapper();
        $users = $userMapper->findAll();
		return $users;
	}
	
	protected function getItem($id = null, $option = null) {
		if($option !== null){
			return parent::getItem($id, $option);
		}
		if($id === null){
			$zfcUserOptions = $this->getZfcUserOptions();
			$class = $zfcUserOptions->getUserEntityClass();
			$user = new $class();
			return $user;
		}
        $user = $this->getUserMapper()->findById($id);
		return $user;
	}
	
	protected function getCreateForm() {
        $form = $this->getServiceLocator()->get('zfcuseradmin_createuser_form');
		return $form;
	}
	
	protected function getEditForm() {
        $form = $this->getServiceLocator()->get('zfcuseradmin_edituser_form');
		return $form;
	}

	protected function _createItem($item, $form, $data = null) {
		$em = $this->getEntityManager();
        $request = $this->getRequest();
		if($data === null){
			$data = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);
		}
		$form->setHydrator(new ClassMethods());
        $form->bind($item);
        $form->setData($data);
        if ($form->isValid()) {
			if(!empty($this->getParentControllerOptions())){
				$parentId = $this->getEvent()->getRouteMatch()->getParam($this->getParentControllerOptions()->getIdParamName());
				$setter = $this->createSetter($this->getOptions()->getParentAttributeName());
				if(method_exists($item, $setter)){
					$parent = $this->getItem($parentId, $this->getParentControllerOptions());
					call_user_func(array($item, $setter), $parent);
				}
			}
			$this->_preCreate($item);
			$user = $this->getAdminUserService()->create($form, $data);
			if(!$user){
				return false;
			}
			$this->_postCreate($item);
			return true;
        }
		return false;
	}
	
	protected function _editItem($item, $form, $data = null) {
		$form->setUser($item);
		
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
                $user = $this->getAdminUserService()->edit($form, $data, $item);
                if ($user) {
					return true;
                }
			}
        } else {
			$form->populateFromUser($item);
		}
		return false;
	}
	
	protected function _delteItem($item) {
		$em = $this->getEntityManager();
		if(!$item){
			return false;
		}
        /** @var $identity \ZfcUser\Entity\UserInterface */
        $identity = $this->zfcUserAuthentication()->getIdentity();
        if ($identity && $identity->getId() == $item->getId()) {
            $this->flashMessenger()->addErrorMessage('You can not delete yourself');
			return false;
        } else {
			if($this->_preDelete($item)){
				$this->getUserMapper()->remove($item);
				$this->_postDelete($item);
				$em->flush();
				return true;
			}
        }
		return false;
	}
	
	public function setZfcUserAdminOptions(ModuleOptions $options){
		$this->zfcUserAdminOptions = $options;
		return $this;
	}

	public function getZfcUserAdminOptions(){
		if (!$this->zfcUserAdminOptions instanceof ModuleOptions) {
			$this->setOptions($this->getServiceLocator()->get('zfcuseradmin_module_options'));
		}
		return $this->zfcUserAdminOptions;
	}

	public function getUserMapper(){
		if (null === $this->userMapper) {
			$this->userMapper = $this->getServiceLocator()->get('zfcuser_user_mapper');
		}
		return $this->userMapper;
	}

	public function setUserMapper(UserInterface $userMapper){
		$this->userMapper = $userMapper;
		return $this;
	}

	public function getAdminUserService(){
		if (null === $this->adminUserService) {
			$this->adminUserService = $this->getServiceLocator()->get('zfcuseradmin_user_service');
		}
		return $this->adminUserService;
	}

	public function setAdminUserService($service){
		$this->adminUserService = $service;
		return $this;
	}

	public function setZfcUserOptions(ZfcUserModuleOptions $options){
		$this->zfcUserOptions = $options;
		return $this;
	}

	/**
	 * @return \ZfcUser\Options\ModuleOptions
	 */
	public function getZfcUserOptions(){
		if (!$this->zfcUserOptions instanceof ZfcUserModuleOptions) {
			$this->setZfcUserOptions($this->getServiceLocator()->get('zfcuser_module_options'));
		}
		return $this->zfcUserOptions;
	}
}