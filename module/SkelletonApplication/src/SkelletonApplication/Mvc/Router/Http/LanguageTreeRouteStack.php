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

namespace SkelletonApplication\Mvc\Router\Http;

use Zend\Mvc\Router\Http\TranslatorAwareTreeRouteStack;
use Zend\Mvc\Router\Http\RouteMatch;
use SkelletonApplication\Options\SkelletonOptions;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Stdlib\RequestInterface;
use SkelletonApplication\Options\SkelletonOptions;

/**
 * Manages multilanguage routes by adding a language key to the baseUrl
 *
 * @author schurix
 */
class LanguageTreeRouteStack extends TranslatorAwareTreeRouteStack {
	
    /**
     * assemble(): defined by \Zend\Mvc\Router\RouteInterface interface.
     *
     * @see    \Zend\Mvc\Router\RouteInterface::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
	public function assemble(array $params = array(), array $options = array()) {
		// Assuming, this stack can only orrur on top level
		// TODO is there any way to ensure that this is called only for top level?
		
		// get translator
		$translator = null;
		if(isset($options['translator'])){
			$translator = $options['translator'];
		} elseif($this->hasTranslator() && $this->isTranslatorEnabled()){
			$translator = $this->getTranslator();
		}
		
		/* @var $skelletonOptions SkelletonOptions */
		$skelletonOptions = $this->getRoutePluginManager()->getServiceLocator()->get(SkelletonOptions::class);
		$languages = $skelletonOptions->getLanguages();
		
		$oldBase = $this->baseUrl; // save old baseUrl
		
		// only add language key when more than one language is supported
		if(count($languages) > 1){
			if(isset($params['locale'])){
				// use parameter if provided
				$locale = $params['locale'];
				$key = array_search($locale, $languages); // get key for locale
			} elseif(is_callable(array($translator, 'getLocale'))){
				// use getLocale if possible
				$locale = $translator->getLocale();
				$key = array_search($locale, $languages); // get key for locale
			}
			
			if(!empty($key)){
				$this->setBaseUrl($oldBase . '/'.$key); // add key to baseUrl
			}
		}
		
		$res = parent::assemble($params, $options);
		$this->setBaseUrl($oldBase); // restore baseUrl
		return $res;
	}
	
    /**
     * match(): defined by \Zend\Mvc\Router\RouteInterface
     *
     * @see    \Zend\Mvc\Router\RouteInterface::match()
     * @param  Request      $request
     * @param  integer|null $pathOffset
     * @param  array        $options
     * @return RouteMatch|null
     */
	public function match(RequestInterface $request, $pathOffset = null, array $options = array()) {
		// do not try to match language when not on top level
		if($pathOffset !== null){
			return parent::match($request, $pathOffset, $options);
		}
		
        if (!method_exists($request, 'getUri')) {
            return null;
        }
		
        if ($this->baseUrl === null && method_exists($request, 'getBaseUrl')) {
            $this->setBaseUrl($request->getBaseUrl());
        }
		
		/* @var $translator TranslatorInterface */
		$translator = null;
		if(isset($options['translator'])){
			$translator = $options['translator'];
		} elseif($this->hasTranslator() && $this->isTranslatorEnabled()){
			$translator = $this->getTranslator();
		}
		
		/* @var $skelletonOptions SkelletonOptions */
		$skelletonOptions = $this->getRoutePluginManager()->getServiceLocator()->get(SkelletonOptions::class);
		$languages = $skelletonOptions->getLanguages();
		$languageKeys = array_keys($languages);
		
		$oldBase = $this->baseUrl;
		$locale = null;
		
		$uri = $request->getUri();
		$baseUrlLength = strlen($this->baseUrl);
		$path = ltrim(substr($uri->getPath(), $baseUrlLength), '/');
		$pathParts = explode('/', $path);
		
		if(count($languages) > 1 && in_array($pathParts[0], $languageKeys)){
			$locale = $languages[$pathParts[0]];
			$this->setBaseUrl($oldBase . '/'.$pathParts[0]);
			if(is_callable(array($translator, 'setLocale'))){
				$translator->setLocale($locale);
			}
		} elseif(!empty($translator) && is_callable(array($translator, 'getLocale'))){
			$locale = $translator->getLocale();
		}
		
		$res = parent::match($request, $pathOffset, $options);
		$this->setBaseUrl($oldBase);
		if($res instanceof RouteMatch && !empty($locale)){
			$res->setParam('locale', $locale);
		}
		return $res;
	}
	
}
