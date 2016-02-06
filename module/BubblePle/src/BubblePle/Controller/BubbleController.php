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
use BubblePle\Entity\Course;
use BubblePle\Entity\Semester;

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
		$em->flush($share);
		
		$isLinked = false;
		$courseParent = $this->findCourseParent($bubble);
		if($courseParent){
			$course = $this->findCourse($courseParent->getCourseroom(), $user);
			if($this->createEdge($course, $bubble)){
				$isLinked = true;
			}
		}
		
		if(!$isLinked){
			$semesterParent = $this->findSemesterParent($bubble);
			if($semesterParent){
				$semester = $this->findSemester($semesterParent->getYear(), $semesterParent->getIsWinter(), $user);
				if(!$semester){
					$semester = new Semester();
					$semester->setYear($semesterParent->getYear())
							->setIsWinter($semesterParent->getIsWinter())
							->setOwner($user);
					$em->persist($semester);
					$em->flush($semester);
				}
				
				if($courseParent){
					$course = new Course();
					$course->setCourseroom($courseParent->getCourseroom())
							->setTitle($courseParent->getTitle())
							->setOwner($user);
					$em->persist($course);
					$em->flush($course);
					$this->createEdge($semester, $course);
					$this->createEdge($course, $bubble);
				} else {
					$this->createEdge($semester, $bubble);
				}
			}
		}
		
		
		return array('success' => true);
	}
	
	protected function edgeExists($from, $to){
		$em = $this->getEntityManager();
		$edgeRepo = $em->getRepository(Edge::class);
		return $edgeRepo->findOneBy(array('from' => $from, 'to' => $to));
	}
	
	protected function removeEdge($from, $to){
		if(!$from || !$to){
			return null;
		}
		
		$exists = $this->edgeExists($from, $to);
		if($exists){
			
			$exists->getTo()->getParents()->removeElement($exists);
			$exists->getFrom()->getChildren()->removeElement($exists);
			$this->getEntityManager()->remove($exists);
			$this->getEntityManager()->flush();
		}
	}
	
	protected function createEdge($from, $to){
		if(!$from || !$to){
			return null;
		}
		
		$exists = $this->edgeExists($from, $to);
		if($exists){
			return $exists;
		}
		
		$edge = new Edge();
		$edge->setFrom($from)
				->setTo($to);
		$this->getEntityManager()->persist($edge);
		$this->getEntityManager()->flush($edge);
		return $edge;
	}
	
	protected function findCourse($courseroom, $user = null){
		if($user === null && !$this->zfcUserAuthentication()->hasIdentity()){
			return null;
		}
		if($user === null){
			$owner = $this->zfcUserAuthentication()->getIdentity();
		} else {
			$owner = $user;
		}
		
		$em = $this->getEntityManager();
		$courseRepo = $em->getRepository(Course::class);
		$course = $courseRepo->findOneBy(array(
			'owner' => $owner,
			'courseroom' => $courseroom,
		));
		return $course;
	}
	
	protected function findSemester($year, $isWinter, $user = null){
		if($user === null && !$this->zfcUserAuthentication()->hasIdentity()){
			return null;
		}
		if($user === null){
			$owner = $this->zfcUserAuthentication()->getIdentity();
		} else {
			$owner = $user;
		}
		
		$em = $this->getEntityManager();
		$semesterRepo = $em->getRepository(Semester::class);
		$semester = $semesterRepo->findOneBy(array(
			'owner' => $owner,
			'year' => $year,
			'isWinter' => $isWinter,
		));
		return $semester;
	}
	
	/**
	 * Returns the first parrent which is an instance of Course
	 * @param Bubble $bubble
	 * @return Course
	 */
	protected function findCourseParent($bubble, $user = null){
		return $this->findParentInstance($bubble, function($item) use ($user) {return $item instanceof Course && ($user == null || $item->getOwner() == $user); });
	}
	
	/**
	 * Returns the first parrent which is an instance of Semester
	 * @param Bubble $bubble
	 * @return Semester
	 */
	protected function findSemesterParent($bubble, $user = null){
		return $this->findParentInstance($bubble, function($item) use ($user) {return $item instanceof Semester && ($user == null || $item->getOwner() == $user); });
	}
	
	protected function findParentInstance($bubble, callable $check){
		$visited = array($bubble);
		$q = array($bubble);
		
		while(!empty($q)){
			$current = array_pop($q);
			if($check($current)){
				return $current;
			}
			$parents = $current->getParents();
			if($parents){
				foreach($parents as $parent){
					if(!in_array($parent->getFrom(), $visited)){
						$q[] = $parent->getFrom();
						$visited[] = $parent->getFrom();
					}
				}
			}
		}
		return null;
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
				$parent = $this->findCourseParent($share->getBubble(), $user);
				if(!$parent){
					$parent = $this->findSemesterParent($share->getBubble(), $user);
				}
				$this->removeEdge($parent, $share->getBubble());
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
