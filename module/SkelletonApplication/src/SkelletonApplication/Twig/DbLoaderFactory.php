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

use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;

use Zend\ServiceManager\FactoryInterface;
/**
 * Description of DbLoaderFactory
 *
 * @author schurix
 */
class DbLoaderFactory implements FactoryInterface{
	
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return MapLoader
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
		$em = $serviceLocator->get(EntityManager::class);
		$translator = $serviceLocator->get('MvcTranslator');

        $loader = new DbLoader();
		$loader->setObjectManager($em);
		$loader->setTranslator($translator);
		
        return $loader;
    }

}
