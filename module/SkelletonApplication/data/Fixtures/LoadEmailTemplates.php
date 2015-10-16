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

/**
 * Description of LoadEmailTemplates
 *
 * @author schurix
 */
class LoadEmailTemplates extends AbstractFixture implements FixtureInterface, ServiceLocatorAwareInterface{
	use ServiceLocatorAwareTrait;
	
	public function load(ObjectManager $manager) {
		// TODO
	}
}
