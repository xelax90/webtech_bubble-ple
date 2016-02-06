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
use Doctrine\Common\Collections\Criteria;

/**
 * Description of BubbleRepository
 *
 * @author schurix
 */
class BubbleRepository extends EntityRepository{
	
	protected function arrayToCriteria($filter){
		if(empty($filter)){
			return null;
		}
		if($filter instanceof Criteria){
			return $filter;
		}
		$criteia = new Criteria();
		foreach($filter as $key => $value){
			if(is_array($value)){
				$expr = $criteia->expr()->eq($key, $value);
			} else {
				$expr = $criteia->expr()->in($key, $value);
			}
			$criteia->andWhere($expr);
		}
		return $criteia;
	}
	
	public function getAccessableBubbles($user, $filter = array(), $order = array()){
		$query = $this->createQueryBuilder('b');
		$query->leftJoin('b.shares', 's');
		$criteria = $this->arrayToCriteria($filter);
		if(!empty($criteria)){
			$query->addCriteria($criteria);
		}
		$query->andWhere($query->expr()->orX('s.sharedWith = :user', 'b.owner = :user'))
			->setParameter('user', $user);
		
		if(!empty($order)){
			foreach($order as $column => $dir){
				if(is_int($column)){
					$query->addOrderBy($dir);
				} else {
					$query->addOrderBy($column, $dir);
				}
			}
		}
		$bubbles = $query->getQuery()->execute();
		return $this->filterChildren(null, $bubbles, $user);
	}
	
	public function getAccessableChildrenOf($user, Bubble $bubble, $filter = array(), $order = array()){
		$bubbles = $this->getAccessableBubbles($user, $filter, $order);
		return $this->filterChildren($bubble, $bubbles, $user);
	}
	
	public function getChildrenOf(Bubble $bubble, $filter = array(), $order = null){
		$bubbles = $this->findBy($filter, $order);
		return $this->filterChildren($bubble, $bubbles);
	}
	
	public function filterChildren(Bubble $parent = null, $bubbles = array(), $user = null){
		if($parent !== null){
			$children = array($parent);
			$q = array($parent);
		} else {
			$children = $bubbles;
			$q = $bubbles;
		}
		while(!empty($q)){
			$currentBubble = array_pop($q);
			/* @var $currentBubble Bubble */
			foreach($currentBubble->getChildren() as $edge){
				/* @var $edge Edge */
				if(!in_array($edge->getTo(), $children, true)){
					$q[] = $edge->getTo();
					$children[] = $edge->getTo();
					
					// If owner is given and the child does not belong to owner, 
					// it must be shared and can be accessed
					if($user !== null && $edge->getTo()->getOwner() !== null && $edge->getTo()->getOwner() !== $user && !in_array($edge->getTo(), $bubbles)){
						$bubbles[] = $edge->getTo();
					}
				}
			}
		}
		
		if(!is_array($bubbles) && ($bubbles instanceof Traversable)){
			$bubbles = $bubbles->toArray();
		}
		
		return array_values(array_intersect($bubbles, $children));
	}
}
