<?php
namespace SkelletonApplication\Entity;

use BjyAuthorize\Provider\Role\ProviderInterface;
use ZfcUser\Entity\User as ZfcUserEntity;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Zend\Json\Json;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * A User.
 *
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends ZfcUserEntity implements JsonSerializable, ProviderInterface
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="user_role_linker",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="user_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    protected $roles;
	
    /**
     * Initialies the roles variable.
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * Get role.
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles->getValues();
    }

    /**
     * Add a role to the user.
     *
     * @param Role $role
     *
     * @return void
     */
    public function addRole($role)
    {
        $this->roles[] = $role;
    }
	
	/**
	 * Returns an array containing data of this object
	 * @return array
	 */
	public function getArrayCopy(){
		return $this->jsonSerialize();
	}
	
	/**
	 * Returns displayName
	 * @return string
	 */
	public function __toString(){
		return $this->getDisplayName();
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
			'user_id' => $this->getId(),
			'username' => $this->getUsername(),
			'email' => $this->getEmail(),
			'displayname' => $this->getDisplayName(),
			'state' => $this->getState(),
			'roles' => $this->getRoles(),
		);
		return $data;
	}
}
