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

use L2PClient\Client as L2PClient;
use Doctrine\ORM\EntityManager;
use BubblePle\Entity\Semester;
use BubblePle\Entity\Course;
use BubblePle\Entity\Edge;
use BubblePle\Entity\FileAttachment;
use BubblePle\Entity\Bubble;
use BubblePle\Entity\L2PMaterialFolder;
use BubblePle\Entity\L2PMaterialAttachment;

/**
 * Description of L2PSync
 *
 * @author schurix
 */
class L2PSync implements ServiceLocatorAwareInterface{
	use ServiceLocatorAwareTrait;
	
	protected $l2p;
	
	protected $auth;
	
	protected $em;
	
	protected $l2pUrl = 'https://www3.elearning.rwth-aachen.de';
	
	/**
	 * 
	 * @return L2PClient
	 */
	public function getClient(){
		if(null === $this->l2p){
			$this->l2p = $this->getServiceLocator()->get(L2PClient::class);
		}
		return $this->l2p;
	}
	
	/**
	 * 
	 * @return \Zend\Authentication\AuthenticationService
	 */
	public function getAuthService(){
		if(null === $this->auth){
			$this->auth = $this->getServiceLocator()->get('zfcuser_auth_service');
		}
		return $this->auth;
	}
	
	/**
	 * 
	 * @return EntityManager
	 */
	public function getEntityManager(){
		if(null === $this->em){
			$this->em = $this->getServiceLocator()->get(EntityManager::class);
		}
		return $this->em;
	}
	
	public function sync(){
		$syncResult = array();
		$l2p = $this->getClient();
		$token = $l2p->getAccessToken();
		if(null === $token){
			$syncResult = array(
				'success' => false,
				'error' => 'No L2P Authorization',
			);
			return $syncResult;
		}
		
		$auth = $this->getAuthService();
		if(!$auth->hasIdentity()){
			$syncResult = array(
				'success' => false,
				'error' => 'No User Authorization',
			);
			return $syncResult;
		}
		
		$syncResult['success'] = true;
		$syncResult['sync'] = array();
		$syncResult['sync']['semester'] = $this->syncSemesters();
		$syncResult['sync']['courses'] = $this->syncCourses();
		$syncResult['sync']['learningMaterial'] = $this->syncLearningMaterial();
		
		return $syncResult;
	}
	
	protected function getSemesterData($short){
		if(!preg_match('/^(ws|ss)[0-9]{2,}$/', $short)){
			return false;
		}
		$isWinter = (substr($short, 0, 2) === 'ws');
		$year = 2000 + substr($short, 2);
		return array('year' => $year, 'isWinter' => $isWinter);
	}
	
	protected function findSemester($short){
		$em = $this->getEntityManager();
		$semesterRepo = $em->getRepository(Semester::class);
		$owner = $this->getAuthService()->getIdentity();
		$semData = $this->getSemesterData($short);
		$found = $semesterRepo->findOneBy(array(
			'owner' => $owner,
			'isWinter' => $semData['isWinter'],
			'year' => $semData['year'],
		));
		return $found;
	}
	
	protected function syncSemesters(){
		$em = $this->getEntityManager();
		$l2p = $this->getClient();
		$owner = $this->getAuthService()->getIdentity();
		
		$response = $l2p->request('viewAllCourseInfo');
		$courseinfo = json_decode($response['output']);
		if($response['code'] != 200){
			return false;
		}
		
		$semesters = array();
		foreach($courseinfo->dataSet as $course){
			if(!in_array($course->semester, $semesters) && preg_match('/(ws|ss)[0-9]{2,}/', $course->semester)){
				$semesters[] = $course->semester;
			}
		}
		foreach($semesters as $semester) {
			$found = $this->findSemester($semester);
			if(!$found){
				$semData = $this->getSemesterData($semester);
				$newSem = new Semester();
				$newSem->setIsWinter($semData['isWinter'])
						->setYear($semData['year'])
						->setOwner($owner);
				$em->persist($newSem);
			}
		}
		$em->flush();
		return true;
	}
	
	protected function syncCourses(){
		$em = $this->getEntityManager();
		$courseRepo = $em->getRepository(Course::class);
		$l2p = $this->getClient();
		$owner = $this->getAuthService()->getIdentity();
		
		$response = $l2p->request('viewAllCourseInfo');
		$courseinfo = json_decode($response['output']);
		if($response['code'] != 200){
			return false;
		}
		
		$result = array(
			'status' => true,
			'new' => array(),
		);
		foreach($courseinfo->dataSet as $course){
			$found = $courseRepo->findOneBy(array(
				'owner' => $owner,
				'courseroom' => $course->uniqueid,
			));
			
			if(!$found){
				$newCourse = new Course();
				$newCourse->setCourseroom($course->uniqueid);
				$newCourse->setTitle($course->courseTitle);
				$newCourse->setOwner($owner);
				$em->persist($newCourse);
				
				$em->flush($newCourse);
				$semester = $this->findSemester($course->semester);
				$newEdge = new Edge();
				$newEdge->setFrom($semester)
						->setTo($newCourse);
				$em->persist($newEdge);
				$em->flush($newEdge);
				
				$result['new'][] = $newCourse->jsonSerialize();
			}
			
		}
		return $result;
		
	}
	
	public function syncLearningMaterial(){
		$em = $this->getEntityManager();
		$courseRepo = $em->getRepository(Course::class);
		$l2p = $this->getClient();
		$owner = $this->getAuthService()->getIdentity();
		$courses = $courseRepo->findBy(array(
			'owner' => $owner,
		));
		$res = array();
		foreach($courses as $course){
			/* @var $course Course */
			$response = $l2p->request('viewAllLearningMaterials', false, array(
				'cid' => $course->getCourseroom(),
			));
			
			if($response['code'] != 200){
				$res[$course->getId()] = false;
				continue;
			}
			$materials = json_decode($response['output']);
			$materialTree = $this->createMaterialTree($materials->dataSet);
			$res[$course->getId()] = $this->syncMaterialTree($materialTree, $course);
		}
		return $res;
	}
	
	protected function syncMaterialTree($materialTree, Bubble $parent){
		$em = $this->getEntityManager();
		$l2p = $this->getClient();
		$owner = $this->getAuthService()->getIdentity();
		
		foreach($materialTree as $material){
			
			// find child matching material
			$children = $parent->getChildren();
			$found = null;
			foreach($children as $child){
				/* @var $child Edge */
				$childBubble = $child->getTo();
				if(
					($material->isDirectory && ($childBubble instanceof L2PMaterialFolder) && $childBubble->getL2pItemId() == $material->itemId) ||
					(!$material->isDirectory && ($childBubble instanceof L2PMaterialAttachment) && $childBubble->getL2pItemId() == $material->itemId)
				){
					$found = $childBubble;
					break;
				}
			}
			
			if(!$found){
				if($material->isDirectory){
					$instance = new L2PMaterialFolder();
				} else {
					$instance = new L2PMaterialAttachment();
					$instance->setFilename($this->l2pUrl . $material->selfUrl);
				}
				$instance->setL2pItemId($material->itemId)
						->setTitle($material->name)
						->setOwner($owner);
				$em->persist($instance);
				$em->flush($instance);
				$edge = new Edge();
				$edge->setFrom($parent)
						->setTo($instance);
				$em->persist($edge);
				$em->flush($edge);
				$found = $instance;
			} else {
				$found->setTitle($material->name);
			}
			
			if($material->isDirectory){
				$this->syncMaterialTree($material->children, $found);
			}
		}
		return true;
	}
	
	protected function createMaterialTree($allMaterials, $parent = 0){
		$children = array();
		foreach($allMaterials as $material){
			if(
				($parent === 0 && $material->itemId == $material->parentFolderId) ||
				($parent !== 0 && $material->itemId != $parent && $material->parentFolderId == $parent)
			){
				$children[] = $material;
				if($material->isDirectory){
					$material->children = $this->createMaterialTree($allMaterials, $material->itemId);
				}
			}
		}
		return $children;
	}
	
}
