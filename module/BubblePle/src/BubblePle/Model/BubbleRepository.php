<?php

/*
 * Copyright (C) 2016 schurix
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

namespace BubblePle\Model;

use Doctrine\ORM\EntityRepository;
use BubblePle\Entity\Bubble;
use BubblePle\Entity\Edge;
use Traversable;

/**
 * Description of BubbleRepository
 *
 * @author schurix
 */
class BubbleRepository extends EntityRepository{
	public function getChildrenOf(Bubble $bubble, $filter = array()){
		$bubbles = $this->findBy($filter);
		$children = array($bubble);
		$q = array($bubble);
		while(!empty($q)){
			$currentBubble = array_pop($q);
			/* @var $currentBubble Bubble */
			foreach($currentBubble->getChildren() as $edge){
				/* @var $edge Edge */
				if(!in_array($edge->getTo(), $children, true)){
					$q[] = $edge->getTo();
					$children[] = $edge->getTo();
				}
			}
		}
		
		if(!is_array($bubbles) && ($bubbles instanceof Traversable)){
			$bubbles = $bubbles->toArray();
		}
		
		return array_values(array_intersect($bubbles, $children));
	}
}
