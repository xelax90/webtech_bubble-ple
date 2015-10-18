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
use SkelletonApplication\Options\SiteRegistrationOptions;
use SkelletonApplication\Form\SiteEmailTemplateForm;
use Doctrine\ORM\EntityManager;
use SkelletonApplication\Entity\Translation;

/**
 * Generic Controller to create a frontend for databse translation editing
 *
 * @author schurix
 */
class EmailTemplateController extends AbstractActionController{
	
	/** @var SiteRegistrationOptions */
	protected $registraionOptions;
	
	/** @var EntityManager */
	protected $entityManager;
	
	/** @var \Zend\Mvc\I18n\Translator */
	protected $translator;
	
	/**
	 * @return SiteRegistrationOptions
	 */
	public function getRegistrationOptions(){
		if(null === $this->registraionOptions){
			$this->registraionOptions = $this->getServiceLocator()->get(SiteRegistrationOptions::class);
		}
		return $this->registraionOptions;
	}
	
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
	 * @return \Zend\Mvc\I18n\Translator
	 */
	public function getTranslator(){
		if(null === $this->translator){
			$this->translator = $this->getServiceLocator()->get('translator');
		}
		return $this->translator;
	}
	
	public function getForm() {
		return $this->getServiceLocator()->get('FormElementManager')->get(SiteEmailTemplateForm::class);
	}
	
	public function getConfig(){
		$translator = $this->getTranslator();
		$registrationOptions = $this->getRegistrationOptions();
		$relevantEmailFlag = $registrationOptions->getRelevantEmailFlag();
		
		$config = array();
		for($i = 0; (1 << $i) <= $relevantEmailFlag; $i++){
			if(!( (1<<$i) & $relevantEmailFlag )){
				continue;
			}
			$emailKey = SiteRegistrationOptions::getEmailKey(1<<$i);
			$config[$emailKey] = array(
				'subject' => $translator->translate(SiteRegistrationOptions::getSubjectTemplateKey(1<<$i)),
				'template' => $translator->translate(SiteRegistrationOptions::getEmailTemplateKey(1<<$i)),
			);
		}
		
		return array('templates' => $config);
	}
	
	public function indexAction() {
		$configForm = $this->getForm();
		$configForm->setData($this->getConfig());
		
		$view = new ViewModel(array('title' => 'E-Mail Templates', 'configForm' => $configForm));
		$view->setTemplate('xelax-site-config/site-config/index.phtml');
		return $view;
	}
	
	/**
	 * Edit config
	 * @return ViewModel
	 */
	public function editAction() {
		$configForm = $this->getForm();
        /* @var $request \Zend\Http\Request */
        $request = $this->getRequest();
		
        if ($request->isPost()) {
			$data = $request->getPost();
			$configForm->setData($data);
			if ($configForm->isValid()) {
				$configData = $configForm->getData();
				$templates = $configData['templates'];
				foreach($templates as $key => $template){
					$this->saveEmail($key, $template['subject'], $template['template']);
				}
				$this->flashMessenger()->addSuccessMessage($this->getTranslator()->translate('Configuration successfully saved'));
				return $this->_redirectToIndex();
			}
        } else {
			$configForm->setData($this->getConfig());
		}
		
		$view = new ViewModel(array('title' => 'E-Mail Templates', 'configForm' => $configForm));
		$view->setTemplate('xelax-site-config/site-config/edit.phtml');
		return $view;
	}
	
	protected function saveEmail($key, $subject, $template){
		$subjectKey = $key.'.subject';
		$templateKey = $key.'.template';
		
		$this->saveTranslation($subjectKey, $subject);
		$this->saveTranslation($templateKey, $template);
	}
	
	protected function saveTranslation($key, $translation){
		$translator = $this->getTranslator();
		$em = $this->getEntityManager();
		$repo = $em->getRepository(Translation::class);
		
		$existing = $repo->findOneBy(array('locale' => $translator->getLocale(), 'textDomain' => 'default', 'translationKey' => $key));
		if(!$existing){
			$existing = new Translation();
			$existing
				->setLocale($translator->getLocale())
				->setTranslationKey($key);
			$em->persist($existing);
		}
		$existing->setTranslation($translation);
		$em->flush($existing);
	}
	
	protected function _redirectToIndex(){
		return $this->redirect()->toRoute(null, array('action' => 'index'));
	}
	
}
