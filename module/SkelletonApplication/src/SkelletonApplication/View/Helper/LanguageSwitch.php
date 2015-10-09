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

namespace SkelletonApplication\View\Helper;

use Zend\I18n\View\Helper\AbstractTranslatorHelper;
use Zend\I18n\Exception;
use SkelletonApplication\Options\SkelletonOptions;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * simple language switch renderer
 *
 * @author schurix
 */
class LanguageSwitch extends AbstractTranslatorHelper implements ServiceLocatorAwareInterface{
	use ServiceLocatorAwareTrait;
	
	const RENDER_TYPE_SELECT = 'select';
	const RENDER_TYPE_DIV = 'div';
	const RENDER_TYPE_NAVBAR = 'navbar';
	const RENDER_TYPE_PARTIAL = 'partial';
	
	protected static $selectFormat = '<select class="%s">%s</select>';
	protected static $optionFormat = '<option %s value="%s">%s</option>';
	protected static $selectClass  = 'language-switch';
	
	protected static $divBoxFormat = '<div class="%s">%s</div>';
	protected static $divOptionFormat = '<div class="%s">%s</div>';
	protected static $divOptionLinkFormat = '<a class="%s" href="%s" lang="%s"></a>';
	protected static $divBoxClass  = 'language-switch';
	protected static $divOptionClass  = 'language-option';
	protected static $divOptionActiveClass  = 'language-option-active';
	protected static $divOptionLanguageClassPrefix  = 'language-option-language-';
	protected static $divOptionLinkClass = 'lang-sm lang-lbl';
	
	protected static $navbarBoxFormat = '<ul class="nav navbar-nav"><li class="%s">%s</li></ul>';
	protected static $navbarOptionsFormat = '<ul class="%s">%s</ul>';
	protected static $navbarOptionFormat = '<li class="%s">%s</li>';
	protected static $navbarCaptionLinkFormat = '<a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="%s" lang="%s"></span><span class="caret"></span></a>';
	protected static $navbarOptionLinkFormat = '<a href="%s"><span class="%s" lang="%s"></span></a>';
	protected static $navbarBoxClass  = 'dropdown';
	protected static $navbarOptionsClass  = 'dropdown-menu';
	protected static $navbarOptionClass  = 'language-option';
	protected static $navbarOptionActiveClass  = 'language-option-active';
	protected static $navbarOptionLanguageClassPrefix  = 'language-option-language-';
	protected static $navbarCaptionLinkClass = 'lang-sm lang-lbl';
	protected static $navbarOptionLinkClass = 'lang-sm lang-lbl';
	
	public function __invoke($currentLocale = null, $renderType = self::RENDER_TYPE_NAVBAR, $config = array()) {
		/* @var $renderer \Zend\View\Renderer\PhpRenderer */
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }
		
		if(isset($config['translator'])){
			$translator = $config['translator'];
		} else {
			$translator = $this->getTranslator();
		}
		if (null === $translator) {
			throw new Exception\RuntimeException('Translator has not been set');
		}
		
		/* @var $options \SkelletonApplication\Options\SkelletonOptions */
		$options = $renderer->getHelperPluginManager()->getServiceLocator()->get(SkelletonOptions::class);
		$locales = $options->getLanguages();
		
		switch($renderType){
			case self::RENDER_TYPE_NAVBAR: return $this->renderNavbar($translator, $locales, $currentLocale, $config);
			case self::RENDER_TYPE_SELECT: return $this->renderSelect($translator, $locales, $currentLocale, $config);
			case self::RENDER_TYPE_DIV: return $this->renderDiv($translator, $locales, $currentLocale, $config);
			case self::RENDER_TYPE_PARTIAL: return $this->renderPartial($translator, $locales, $currentLocale, $config);
		}
		throw new Exception\InvalidArgumentException(sprintf('Invalid render type %s', $renderType));
	}
	
	/**
	 * @return \Zend\Mvc\Router\RouteMatch
	 */
	protected function getRouteMatch(){
		$routeMatch = $this->getServiceLocator()->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch();
		return $routeMatch;
	}
	
	protected function renderSelect($translator, $locales, $currentLocale = null, $config = array()){
		// check config and set variables
		$selectClass = static::$selectClass;
		if(isset($config['selectClass'])){
			$selectClass = $config['selectClass'];
		}
		
		if(null === $currentLocale){
			// detect current locale if not given
			if(is_callable(array($translator, 'getLocale'))){
				$currentLocale = $translator->getLocale();
			}
		}
		
		// build options
		$options = '';
		foreach($locales as $localeKey => $locale){
			$selected = '';
			if($locale === $currentLocale){
				$selected = 'selected="selected"';
			}
			$options .= sprintf(static::$optionFormat, $selected, $locale, $localeKey);
		}
		// return select with options
		return sprintf(static::$selectFormat, $selectClass, $options);
	}
	
	protected function renderDiv($translator, $locales, $currentLocale = null, $config = array()){
		// check config and set variables
		$boxClass = static::$divBoxClass;
		if(isset($config['boxClass'])){
			$boxClass = $config['boxClass'];
		}
		$optionClass = static::$divOptionClass;
		if(isset($config['optionClass'])){
			$optionClass = $config['optionClass'];
		}
		$optionActiveClass = static::$divOptionActiveClass;
		if(isset($config['optionActiveClass'])){
			$optionActiveClass = $config['optionActiveClass'];
		}
		$optionLanguageClassPrefix = static::$divOptionLanguageClassPrefix;
		if(isset($config['optionLanguageClassPrefix'])){
			$optionLanguageClassPrefix = $config['optionLanguageClassPrefix'];
		}
		$optionLinkClass = static::$divOptionLinkClass;
		if(isset($config['optionLinkClass'])){
			$optionLinkClass = $config['optionLinkClass'];
		}
		
		if(null === $currentLocale){
			// detect current locale if not given
			if(is_callable(array($translator, 'getLocale'))){
				$currentLocale = $translator->getLocale();
			}
		}
		$urlPlugin = $this->getView()->plugin('url');
		
		// build options
		$options = '';
		foreach($locales as $localeKey => $locale){
			$optClass = $optionClass;
			if($locale === $currentLocale){
				$optClass .= ' '.$optionActiveClass;
			}
			$optClass .= ' '.$optionLanguageClassPrefix.$locale;
			$parameters = $this->getRouteMatch()->getParams();
			$parameters['locale'] = $locale;
			$url = $urlPlugin(null, $parameters);
			$link = sprintf(static::$divOptionLinkFormat, $optionLinkClass, $url, $localeKey);
			$options .= sprintf(static::$divOptionFormat, $optClass, $link);
		}
		// return container with options
		return sprintf(static::$divBoxFormat, $boxClass, $options);
	}
	
	protected function renderNavbar($translator, $locales, $currentLocale = null, $config = array()){
		// check config and set variables
		$boxClass = static::$navbarBoxClass;
		if(isset($config['boxClass'])){
			$boxClass = $config['boxClass'];
		}
		$optionsClass = static::$navbarOptionsClass;
		if(isset($config['optionsClass'])){
			$optionsClass = $config['optionsClass'];
		}
		$optionClass = static::$navbarOptionClass;
		if(isset($config['optionClass'])){
			$optionClass = $config['optionClass'];
		}
		$optionActiveClass = static::$navbarOptionActiveClass;
		if(isset($config['optionActiveClass'])){
			$optionActiveClass = $config['optionActiveClass'];
		}
		$optionLanguageClassPrefix = static::$navbarOptionLanguageClassPrefix;
		if(isset($config['optionLanguageClassPrefix'])){
			$optionLanguageClassPrefix = $config['optionLanguageClassPrefix'];
		}
		$optionLinkClass = static::$navbarOptionLinkClass;
		if(isset($config['optionLinkClass'])){
			$optionLinkClass = $config['optionLinkClass'];
		}
		$captionLinkClass = static::$navbarCaptionLinkClass;
		if(isset($config['captionLinkClass'])){
			$captionLinkClass = $config['captionLinkClass'];
		}
		
		if(null === $currentLocale){
			// detect current locale if not given
			if(is_callable(array($translator, 'getLocale'))){
				$currentLocale = $translator->getLocale();
			}
		}
		$urlPlugin = $this->getView()->plugin('url');
		
		// build options
		$options = '';
		foreach($locales as $localeKey => $locale){
			$optClass = $optionClass;
			if($locale === $currentLocale){
				$optClass .= ' '.$optionActiveClass;
			}
			$optClass .= ' '.$optionLanguageClassPrefix.$locale;
			$parameters = $this->getRouteMatch()->getParams();
			$parameters['locale'] = $locale;
			$url = $urlPlugin(null, $parameters);
			$link = sprintf(static::$navbarOptionLinkFormat, $url, $optionLinkClass, $localeKey);
			$options .= sprintf(static::$navbarOptionFormat, $optClass, $link);
		}
		
		$captionKey = array_search($currentLocale, $locales);
		if($captionKey === false && !empty($currentLocale)){
			$captionKey = substr($currentLocale, 0, strpos($currentLocale, '_'));
		} elseif($captionKey === false) {
			$captionKey = array_keys($locales)[0];
		}
		
		// return container with options
		return sprintf(static::$navbarBoxFormat, 
			$boxClass,
			sprintf(static::$navbarCaptionLinkFormat, $captionLinkClass, $captionKey).
			sprintf(static::$navbarOptionsFormat, $optionsClass, $options)
		);
	}


	protected function renderPartial($translator, $locales, $currentLocale = null, $config = array()){
		if(!isset($config['partial'])){
			throw new Exception\InvalidArgumentException('Partial is not set');
		}
		
		$options = array(
			'translator' => $translator,
			'locales' => $locales,
			'currentLocale' => $currentLocale,
			'config' => $config
		);
		
		return $this->getView()->render($config['partial'], $options);
	}
	
}
