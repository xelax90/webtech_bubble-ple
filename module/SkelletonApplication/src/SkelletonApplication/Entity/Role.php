<?php

namespace SkelletonApplication\Entity;

use BjyAuthorize\Acl\HierarchicalRoleInterface;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Zend\Json\Json;

/**
 * A hirarcical role
 *
 * @ORM\Entity
 * @ORM\Table(name="role")
 */
class Role implements HierarchicalRoleInterface, JsonSerializable
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    protected $roleId;

    /**
     * @var Role
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="children")
     */
    protected $parent;
	
	/**
	 * @var \Doctrine\Common\Collections\Collection 
	 * @ORM\OneToMany(targetEntity="Role", mappedBy="parent")
	 */
	protected $children;
	
    /**
     * Get the id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the id.
     *
     * @param int $id
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = (int)$id;
    }

    /**
     * Get the role id.
     *
     * @return string
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * Set the role id.
     *
     * @param string $roleId
     *
     * @return void
     */
    public function setRoleId($roleId)
    {
        $this->roleId = (string) $roleId;
    }

    /**
     * Get the parent role
     *
     * @return Role
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the parent role.
     *
     * @param Role $role
     *
     * @return void
     */
    public function setParent(Role $parent = null)
    {
        $this->parent = $parent;
    }
	
	/**
	 * Calculates depth level of this role
	 * @return int
	 */
	public function getLevel(){
		if ($this->getParent() == null) {
			return 0;
		}
		return 1 + $this->getParent()->getLevel();
	}
	
	/**
	 * Returns all child roles
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getChildren(){
		return $this->children;
	}
	
	/**
	 * Returns an array containing data of this object
	 * @return array
	 */
	public function getArrayCopy(){
		return $this->jsonSerialize();
	}
	
	/**
	 * Returns roleId
	 * @return string
	 */
	public function __toString(){
		return $this->getRoleId();
	}
	
	/**
	 * Returns JSON String, representing this object
	 * @return string
	 */
	public function toJson(){
		$data = $this->jsonSerialize();
		return Json::encode($data, true, array('silenceCyclicalExceptions' => true));
	}
	
	/**
	 * Returns array of data, representing this object
	 * @return array
	 */
	public function jsonSerialize(){
		$data = array(
			'id' => $this->getId(),
			'roleId' => $this->getRoleId(),
			'parent' => $this->getParent(),
			'children' => $this->getChildren()
		);
		return $data;
	}

}
