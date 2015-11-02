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

namespace SkelletonApplication\Fixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use SkelletonApplication\Entity\Translation;
use SkelletonApplication\Options\SiteRegistrationOptions;

/**
 * Loads user notification e-mail templates
 *
 * @author schurix
 */
class LoadEmailTemplates extends AbstractFixture implements FixtureInterface, ServiceLocatorAwareInterface{
	use ServiceLocatorAwareTrait;
	
	const EMAIL_MSB = 8;
	
	protected function getEmailSubject($key, $language){
		$subjects = array(
			SiteRegistrationOptions::getSubjectTemplateKey(SiteRegistrationOptions::REGISTRATION_EMAIL_ACTIVATED) => array(
				'de_DE' => '[SkelletonApplication] Dein Account wurde bestätigt',
				'en_US' => '[SkelletonApplication] Your Account has been verified'
			),
			SiteRegistrationOptions::getSubjectTemplateKey(SiteRegistrationOptions::REGISTRATION_EMAIL_CONFIRM_MAIL) => array(
				'de_DE' => '[SkelletonApplication] Willkommen. Bitte bestätige deine E-Mail Adresse',
				'en_US' => '[SkelletonApplication] Welcome. Please confirm your E-Mail'
			),
			SiteRegistrationOptions::getSubjectTemplateKey(SiteRegistrationOptions::REGISTRATION_EMAIL_CONFIRM_MODERATOR) => array(
				'de_DE' => '[SkelletonApplication] Willkommen',
				'en_US' => '[SkelletonApplication] Welcome'
			),
			SiteRegistrationOptions::getSubjectTemplateKey(SiteRegistrationOptions::REGISTRATION_EMAIL_DISABLED) => array(
				'de_DE' => '[SkelletonApplication] Dein Account wurde gesperrt',
				'en_US' => '[SkelletonApplication] Your Account has been disabled'
			),
			SiteRegistrationOptions::getSubjectTemplateKey(SiteRegistrationOptions::REGISTRATION_EMAIL_DOUBLE_CONFIRM_MAIL) => array(
				'de_DE' => '[SkelletonApplication] Willkommen',
				'en_US' => '[SkelletonApplication] Welcome'
			),
			SiteRegistrationOptions::getSubjectTemplateKey(SiteRegistrationOptions::REGISTRATION_EMAIL_MODERATOR) => array(
				'de_DE' => '[SkelletonApplication] Ein neuer Benutzer hat sich registriert',
				'en_US' => '[SkelletonApplication] A new user has registered'
			),
			SiteRegistrationOptions::getSubjectTemplateKey(SiteRegistrationOptions::REGISTRATION_EMAIL_WELCOME) => array(
				'de_DE' => '[SkelletonApplication] Willkommen',
				'en_US' => '[SkelletonApplication] Welcome'
			),
			SiteRegistrationOptions::getSubjectTemplateKey(SiteRegistrationOptions::REGISTRATION_EMAIL_WELCOME_CONFIRM_MAIL) => array(
				'de_DE' => '[SkelletonApplication] Willkommen. Bitte bestätige deine E-Mail Adresse',
				'en_US' => '[SkelletonApplication] Welcome. Please confirm your E-Mail'
			),
		);
		return $subjects[$key][$language];
	}
	
	public function load(ObjectManager $manager) {
		$templateDir = dirname(__DIR__).'/email_templates';
		$languages = scandir($templateDir);
		foreach($languages as $language){
			if($language{0} === '.'){
				continue;
			}
			$languageDir = $templateDir.DIRECTORY_SEPARATOR.$language;
			if(!is_dir($languageDir) || !is_readable($languageDir)){
				continue;
			}
			
			$templates = scandir($languageDir);
			
			foreach($templates as $template){
				if($template{0} === '.'){
					continue;
				}
				
				$templateFile = $languageDir.DIRECTORY_SEPARATOR.$template;
				if(!is_readable($templateFile) || !is_file($templateFile) || pathinfo($templateFile, PATHINFO_EXTENSION) !== 'twig'){
					echo 'ignoring template '.$templateFile.PHP_EOL;
					continue;
				}
				
				$translationKey = 'skelleton.email.registration.'.basename($templateFile, '.'.pathinfo($templateFile, PATHINFO_EXTENSION)).'.template';
				
				$found = $manager->getRepository(Translation::class)->findOneBy(array('locale' => $language, 'textDomain' => 'default', 'translationKey' => $translationKey));
				if($found){
					$translation = $found;
				} else {
					$translation = new Translation();
					$translation
						->setLocale($language)
						->setTranslationKey($translationKey);
					$manager->persist($translation);
				}
				$translation->setTranslation(file_get_contents($templateFile));
				
			}
		}
		
		for($i = 0; $i < static::EMAIL_MSB; $i++){
			foreach($languages as $language){
				if($language{0} === '.'){
					continue;
				}
				$languageDir = $templateDir.DIRECTORY_SEPARATOR.$language;
				if(!is_dir($languageDir) || !is_readable($languageDir)){
					continue;
				}
				
				$subjectKey = SiteRegistrationOptions::getSubjectTemplateKey(1<<$i);
				
				$found = $manager->getRepository(Translation::class)->findOneBy(array('locale' => $language, 'textDomain' => 'default', 'translationKey' => $subjectKey));
				if($found){
					$translation = $found;
				} else {
					$translation = new Translation();
					$translation
						->setLocale($language)
						->setTranslationKey($subjectKey);
					$manager->persist($translation);
				}
				$translation->setTranslation($this->getEmailSubject($subjectKey, $language));
			}
		}
		
				
		$manager->flush();
	}
}
