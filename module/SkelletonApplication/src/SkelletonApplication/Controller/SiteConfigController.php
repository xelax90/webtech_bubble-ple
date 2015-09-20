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

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use SkelletonApplication\Form\ConfigEmailForm;
use Eye4web\SiteConfig\Service\SiteConfigAwareInterface;
use Eye4web\SiteConfig\Service\SiteConfigAwareTrait;
use Doctrine\ORM\EntityManager;
use SkelletonApplication\Entity\SiteConfig;
use SkelletonApplication\Options\SiteEmailOptions;
use Zend\I18n\Translator\Translator;

/**
 * Description of SiteConfigController
 *
 * @author schurix
 */
class SiteConfigController extends AbstractActionController implements SiteConfigAwareInterface{
	use SiteConfigAwareTrait;
	
	/** @var SiteEmailOptions */
	protected $emailConfig;
	
	/** @var EntityManager */
	protected $entityManager;
	
	/** @var Translator */
	protected $translator = null;
	
	/**
	 * @return EntityManager
	 */
	public function getEntityManager(){
		if(null === $this->entityManager){
			$this->entityManager = $this->getServiceLocator()->get(EntityManager::class);
		}
		return $this->entityManager;
	}
	
	/**
	 * @return SiteEmailOptions
	 */
	public function getEmailConfig(){
		if(null === $this->emailConfig){
			$this->emailConfig = $this->getServiceLocator()->get('SkelletonApplication\Options\Site\Email');
		}
		return $this->emailConfig;
	}
	
	/**
	 * @return Translator
	 */
	public function getTranslator() {
		if(null === $this->translator){
			$this->translator = $this->getServiceLocator()->get('MVCTranslator');
		}
		return $this->translator;
	}
	
	public function indexAction(){
		/* @var $emailForm ConfigEmailForm */
		$emailForm = $this->getServiceLocator()->get('FormElementManager')->get(ConfigEmailForm::class);
		$emailForm->setData($this->getEmailConfig());
		
		return new ViewModel(array('emailForm' => $emailForm));
	}
	
	
	public function emailAction() {
		/* @var $emailForm ConfigEmailForm */
		$emailForm = $this->getServiceLocator()->get('FormElementManager')->get(ConfigEmailForm::class);
        /* @var $request \Zend\Http\Request */
        $request = $this->getRequest();
		
        if ($request->isPost()) {
			$data = $request->getPost();
			$emailForm->setData($data);
			if ($emailForm->isValid()) {
				$configData = $emailForm->getData();
				$flatConfig = $this->flattenConfig($configData['configemail'], 'skelletonapplication.email');
				$this->saveConfig($flatConfig);
				
				$this->flashMessenger()->addSuccessMessage($this->getTranslator()->translate('Config successfully saved'));
				return $this->_redirectToIndex();
			}
        } else {
			$emailForm->setData($this->getEmailConfig());
		}
		
		return new ViewModel(array('emailForm' => $emailForm));
	}
	
	protected function _redirectToIndex(){
		return $this->redirect()->toRoute('zfcadmin/siteconfig', array('action' => 'index'));
	}
	
	protected function flattenConfig($config, $prefix = ''){
		$prefix = ltrim($prefix, '.');
		$res = array();
		foreach ($config as $key => $value) {
			if(is_array($value)){
				$flat = $this->flattenConfig($value,  $prefix.'.'.$key);
				$res = array_merge($res, $flat);
			} else {
				$res[ltrim($prefix.'.'.$key, '.')] = $value;
			}
		}
		return $res;
	}
	
	protected function saveConfig($config){
		$em = $this->getEntityManager();
		$repo = $em->getRepository(SiteConfig::class);
		
		foreach ($config as $key => $value) {
			$existing = $repo->findOneBy(array('confKey' => $key));
			if(!$existing){
				$existing = new SiteConfig();
				$existing->setKey($key);
				$em->persist($existing);
			}
			$existing->setValue($value);
		}
		return $em->flush();
	}
}
