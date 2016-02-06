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
use BubblePle\Entity\Bubble;
use BubblePle\Entity\Edge;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Exception;
use BubblePle\Service\BubblePermission;
use BubblePle\Entity\BubbleShare;
use SkelletonApplication\Entity\User;


/**
 * Controller that handles bubbles
 *
 * @author schurix
 */
class BubbleController extends ListController{
	
	protected $bubblePermission;
	
	protected $userMapper;
	
	/**
	 * @return BubblePermission
	 */
	public function getBubblePermission(){
		if(null == $this->bubblePermission){
			$this->bubblePermission = $this->getServiceLocator()->get(BubblePermission::class);
		}
		return $this->bubblePermission;
	}
	
	/**
	 * @return \ZfcUser\Mapper\UserInterface
	 */
	public function getUserMapper(){
		if(null == $this->userMapper){
			$this->userMapper = $this->getServiceLocator()->get('zfcuser_user_mapper');
		}
		return $this->userMapper;
	}
	
	/**
	 * Returns list of all items to show in list view. Overwrite to add custom filters
	 * @return \Traversable
	 */
	protected function getAll(){
		return $this->getAllOrdered();
	}
	
	/**
	 * Returns list of all items to show in list view. Overwrite to add custom filters
	 * @return \Traversable
	 */
	protected function getAllOrdered($order = array()){
		if(!$this->zfcUserAuthentication()->hasIdentity()){
			return array();
		}
		
		$em = $this->getEntityManager();
		$entityClass = $this->getEntityClass();
		
		$user = $this->zfcUserAuthentication()->getIdentity();
		
		$params = array();
		
		if(!empty($this->getParentControllerOptions())){
			$parentId = $this->getEvent()->getRouteMatch()->getParam($this->getParentControllerOptions()->getIdParamName());
			$params[$this->getOptions()->getParentAttributeName()] = $parentId;
		}
		
		$repo = $em->getRepository($entityClass);
		if($repo instanceof \BubblePle\Model\BubbleRepository){
			$items = $repo->getAccessableBubbles($user, $params, $order);
		} else {
			$items = $repo->findBy($params, $order);
		}
		
		return $items;
	}
	
	protected function getItem($id = null, $option = null) {
		if(!$this->zfcUserAuthentication()->hasIdentity()){
			return null;
		}
		$item = parent::getItem($id, $option);
		
		if($id !== null && $option === null && !$this->getBubblePermission()->canView($item)){
			return null;
		}
		
		return $item;
	}
	
	protected function _preCreate($item) {
		parent::_preCreate($item);
		$user = $this->zfcUserAuthentication()->getIdentity();
		$item->setOwner($user);
	}
	
	protected function _editItem($item, $form, $data = null) {
		if(!$this->getBubblePermission()->canEdit($item)){
			$this->flashMessenger()->addErrorMessage($this->getTranslator()->translate('Not authorized'));
			return false;
		}
		return parent::_editItem($item, $form, $data);
	}
	
	protected function _delteItem($item) {
		if(!$this->getBubblePermission()->canDelete($item)){
			$this->flashMessenger()->addErrorMessage($this->getTranslator()->translate('Not authorized'));
			return false;
		}
		return parent::_delteItem($item);
	}
	
	public function filterAction(){
		return new JsonModel($this->filter());
	}
	
	protected function filter(){
		if(!$this->zfcUserAuthentication()->hasIdentity()){
			return array('success' => false, 'error' => 'Not authenticated');
		}
		
		$em = $this->getEntityManager();
		$bRepo = $em->getRepository(Bubble::class);
		/* @var $bRepo \BubblePle\Model\BubbleRepository */
		$eRepo = $em->getRepository(Edge::class);
		/* @var $eRepo \BubblePle\Model\EdgeRepository */
		
		$parent = (int) $this->getEvent()->getRouteMatch()->getParam('parent');
		$parentBubble = $bRepo->find($parent);
		if(!$this->getBubblePermission()->canView($parentBubble)){
			$parentBubble = null;
		}
		
		if(!$parentBubble){
			return array('success' => false, 'error' => 'Not allowed');
		}
		
		$children = $bRepo->getAccessableChildrenOf($this->zfcUserAuthentication()->getIdentity(), $parentBubble);
		$edges = $eRepo->getConnectingEdges($children);
		
		$result = array(
			'success' => true,
			'bubbles' => $children,
			'edges' => $edges,
		);
		
		return $result;
	}
	
	public function renderFormAction(){
		$bubbleType = $this->getEvent()->getRouteMatch()->getParam('bubbleType');
		$bubbleId = (int) $this->getEvent()->getRouteMatch()->getParam('bubbleId');
		
		$form = $this->getFormForBubble($bubbleType);
		$url = $this->getUrlForBubble($bubbleType, $bubbleId);
		
		$viewModel = new ViewModel();
		$viewModel->setTerminal(true);
		$viewModel->setVariables(array(
			'form' => $form,
			'url' => $url,
		));
		return $viewModel;
	}
	
	public function syncAction(){
		$syncService = $this->getServiceLocator()->get(\BubblePle\Service\L2PSync::class);
		/* @var $syncService \BubblePle\Service\L2PSync */
		$syncResult = $syncService->sync();
		return new JsonModel($syncResult);
	}
	
	protected function getUrlForBubble($bubbleType, $id = 0){
		$bubbleParts = explode('\\', $bubbleType);
		$routeName = lcfirst($bubbleParts[count($bubbleParts) - 1]).'s';
		$url = '';
		try{
			$parameters = array(
				'action' => 'rest',
			);
			$url = $this->url()->fromRoute('zfcadmin/bubblePLE/'.$routeName, $parameters);
			if(!empty($id)){
				$url .= '/'.$id;
			}
		} catch (Exception $ex) {
		}
		return $url;
	}
	
	protected function getFormForBubble($bubbleType){
		if(empty($bubbleType)){
			return null;
		}
		if(!class_exists($bubbleType)){
			return null;
		}
		
		$nameParts = explode('\\', $bubbleType.'Form');
		$nameParts[1] = 'Form';
		$formClass = implode('\\', $nameParts);
		if(!class_exists($formClass)){
			return null;
		}
		return $this->getServiceLocator()->get('FormElementManager')->get($formClass);
	}
	
	public function shareAction(){
		$bubbleId = (int) $this->getEvent()->getRouteMatch()->getParam('bubbleId');
		$userId = (int) $this->getEvent()->getRouteMatch()->getParam('userId');
		return new JsonModel($this->shareBubble($bubbleId, $userId));
	}
	
	protected function shareBubble($bubbleId, $userId){
		$user = $this->getUserMapper()->findById($userId);
		if(!$user){
			return array( 'success' => false, 'error' => 'User not found' );
		}
		
		$em = $this->getEntityManager();
		$bubbleRepo = $em->getRepository(Bubble::class);
		
		$bubble = $bubbleRepo->find($bubbleId);
		if(!$bubble){
			return array('success' => false, 'error' => 'Bubble not found' );
		}
		
		if(!$this->getBubblePermission()->canShare($bubble)){
			return array('success' => false, 'error' => 'Not authorized');
		}
		
		$share = new BubbleShare();
		$share->setBubble($bubble)
				->setSharedWith($user);
		$em->persist($share);
		$em->flush();
		
		return array('success' => true);
	}
	
	public function unShareAction(){
		$bubbleId = (int) $this->getEvent()->getRouteMatch()->getParam('bubbleId');
		$userId = (int) $this->getEvent()->getRouteMatch()->getParam('userId');
		return new JsonModel($this->unShareBubble($bubbleId, $userId));
	}
	
	protected function unShareBubble($bubbleId, $userId){
		$user = $this->getUserMapper()->findById($userId);
		if(!$user){
			return array( 'success' => false, 'error' => 'User not found' );
		}
		
		$em = $this->getEntityManager();
		$bubbleRepo = $em->getRepository(Bubble::class);
		
		/* @var $bubble Bubble */
		$bubble = $bubbleRepo->find($bubbleId);
		if(!$bubble){
			return array('success' => false, 'error' => 'Bubble not found' );
		}
		
		if(!$this->getBubblePermission()->canShare($bubble)){
			return array('success' => false, 'error' => 'Not authorized');
		}
		
		$shares = $bubble->getShares();
		foreach($shares as $share){
			if($share->getSharedWith() == $user){
				$em->remove($share);
			}
		}
		$em->flush();
		
		return array('success' => true);
	}
	
	public function usernamesAction(){
		$userRepo = $this->getEntityManager()->getRepository(User::class);
		$users = $userRepo->findAll();
		$res = array();
		foreach($users as $user){
			$res[] = array(
				'name' => $user->getDisplayName() ?: 'User '.$user->getId(),
				'id' => $user->getId(),
			);
		}
		return new JsonModel($res);
	}
}
