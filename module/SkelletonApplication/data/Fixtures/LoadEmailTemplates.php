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

/**
 * Description of LoadEmailTemplates
 *
 * @author schurix
 */
class LoadEmailTemplates extends AbstractFixture implements FixtureInterface, ServiceLocatorAwareInterface{
	use ServiceLocatorAwareTrait;
	
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
					echo 'ignoring template '.$templateFile;
					continue;
				}
				
				$translationKey = 'skelleton.email.registration.'.basename($templateFile, '.'.pathinfo($templateFile, PATHINFO_EXTENSION));
				
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
		$manager->flush();
	}
}
