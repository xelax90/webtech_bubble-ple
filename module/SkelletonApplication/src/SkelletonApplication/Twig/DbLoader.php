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

namespace SkelletonApplication\Twig;

use Twig_Error_Loader;
use Twig_ExistsLoaderInterface;
use Twig_LoaderInterface;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Zend\I18n\Translator\TranslatorAwareInterface;
use Zend\I18n\Translator\TranslatorAwareTrait;

/**
 * Description of DbLoader
 *
 * @author schurix
 */
class DbLoader implements Twig_ExistsLoaderInterface, Twig_LoaderInterface, ObjectManagerAwareInterface, TranslatorAwareInterface{
	use ProvidesObjectManager, TranslatorAwareTrait;
	
	public function exists($name) {
		$translator = $this->getTranslator();
		return $translator->translate($name) != $name;
	}

	public function getCacheKey($name) {
		$translator = $this->getTranslator();
		return $this->getTranslatorTextDomain().'/'.$translator->getLocale().'/'.$name;
	}

	public function getSource($name) {
		$translator = $this->getTranslator();
		return $translator->translate($name);
	}

	public function isFresh($name, $time) {
		// TODO
		return false;
	}
}
