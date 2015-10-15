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

namespace SkelletonApplication\I18n\Translator\Loader;

use Zend\I18n\Translator\Loader\RemoteLoaderInterface;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use SkelletonApplication\Entity\Translation;
use Zend\I18n\Translator\TextDomain;

/**
 * Database translation loader
 *
 * @author schurix
 */
class Db implements RemoteLoaderInterface, ObjectManagerAwareInterface{
	use ProvidesObjectManager;
	
	/**
	 * {@inheritDoc}
	 */
	public function load($locale, $textDomain) {
		$repository = $this->getObjectManager()->getRepository(Translation::class);
		// get all tanslations for namespace and locale
		$translations = $repository->findBy(array(
			'locale' => $locale,
			'textDomain' => $textDomain,
		));
		
		// create TextDomain object
		$messages = array();
		foreach($translations as $translation){
			/* @var $translation Translation */
			$messages[$translation->getTranslationkey()] = $translation->getTranslation();
		}
		return new TextDomain($messages);
	}
}
