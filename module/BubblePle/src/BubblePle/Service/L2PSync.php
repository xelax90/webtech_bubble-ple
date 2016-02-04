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
	
}
