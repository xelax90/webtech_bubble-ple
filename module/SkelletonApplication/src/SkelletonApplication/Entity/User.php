<?php
namespace SkelletonApplication\Entity;

use BjyAuthorize\Provider\Role\ProviderInterface;
use ZfcUser\Entity\User as ZfcUserEntity;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Zend\Json\Json;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;

/**
 * A User.
 * 
 * @ORM\Entity(repositoryClass="SkelletonApplication\Model\UserRepository")
 * @ORM\Table(name="user")
 * @ORM\HasLifecycleCallbacks
 */
class User extends ZfcUserEntity implements JsonSerializable, ProviderInterface
{
	const STATE_ACTIVE_BIT = 0;
	const STATE_EMAIL_BIT = 1;
	
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
	 * @var UserProfile
	 * @ORM\OneToOne(targetEntity="UserProfile", mappedBy="user", cascade={"remove"})
	 */
	protected $profile;
	
	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	protected $token;
	
	/**
	 * @var DateTime
	 * @ORM\Column(type="datetime")
	 */
	protected $tokenCreatedAt;
	
	/**
	 * @var DateTime
	 * @ORM\Column(type="datetime")
	 */
	protected $createdAt;
	
	/**
	 * @var DateTime
	 * @ORM\Column(type="datetime")
	 */
	protected $updatedAt;
	
	
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
     * Add roles to the user.
     *
     * @param \Doctrine\Common\Collections\Collection $roles
     */
    public function addRoles($roles)
    {
		foreach($roles as $role){
			$this->roles->add($role);
		}
    }
	
	/**
	 * Add a role to the user
	 * @param Role $role
	 */
	public function addRole($role){
		$this->roles->add($role);
	}
	
	/**
	 * Remove roles from the user
	 * 
	 * @param \Doctrine\Common\Collections\Collection $roles
	 */
	public function removeRoles($roles){
		foreach($roles as $role){
			$this->roles->removeElement($role);
		}
	}
	
	/**
	 * Returns the user profile or null.
	 * @return UserProfile
	 */
	public function getProfile() {
		return $this->profile;
	}
	
	/**
	 * Sets the profile. 
	 * @param UserProfile $profile
	 * @return User
	 */
	public function setProfile($profile) {
		$this->profile = $profile;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getToken() {
		return $this->token;
	}
	
	/**
	 * @return DateTime
	 */
	public function getTokenCreatedAt() {
		return $this->tokenCreatedAt;
	}

	/**
	 * @return DateTime
	 */
	public function getCreatedAt() {
		return $this->createdAt;
	}

	/**
	 * @return DateTime
	 */
	public function getUpdatedAt() {
		return $this->updatedAt;
	}
	
	/**
	 * @param string $token
	 * @return User
	 */
	public function setToken($token) {
		$this->token = $token;
		return $this;
	}

	/**
	 * @param DateTime $tokenCreatedAt
	 * @return User
	 */
	public function setTokenCreatedAt($tokenCreatedAt) {
		$this->tokenCreatedAt = $tokenCreatedAt;
		return $this;
	}
	
	/**
	 * @param DateTime $createdAt
	 * @return User
	 */
	public function setCreatedAt($createdAt) {
		$this->createdAt = $createdAt;
		return $this;
	}
	
	/**
	 * @param DateTime $updatedAt
	 * @return User
	 */
	public function setUpdatedAt($updatedAt) {
		$this->updatedAt = $updatedAt;
		return $this;
	}
	
	/**
	 * Returns true if the user is active (aka can sign in)
	 * @return boolean
	 */
	public function isActive(){
		return $this->getState() & (1 << static::STATE_ACTIVE_BIT) !== 0;
	}
	
	/**
	 * Returns true if the user has verified his email
	 * @return boolean
	 */
	public function isEmailVerified(){
		return $this->getState() & (1 << static::STATE_EMAIL_BIT) !== 0;
	}
	
	public function setIsActive($isActive){
		$this->setState($this->setBitTo($this->getState(), static::STATE_ACTIVE_BIT, $isActive));
	}
	
	public function setEmailIsVerified($isVerified){
		$this->setState($this->setBitTo($this->getState(), static::STATE_EMAIL_BIT, $isVerified));
	}
	
	protected function setBitTo($mask, $bit, $value){
		$mask = $mask & ~(1 << $bit);
		return $mask | (!!$value << $bit);
	}
	
	/**
	 * Generates token for email verification
	 */
	protected function generateToken(){
		$this->setToken(strtoupper(substr(sha1(
            $this->getEmail() . 
            '0#c#n#c#r#u0#y#h7' . 
            strtotime($this->getTokenCreatedAt())
        ),0,15)));
	}

	/** 
	 * @ORM\PrePersist 
	 */  
	public function prePersist()  
	{
		$this->createdAt = new DateTime();
		$this->updatedAt = new DateTime();
		$this->setTokenCreatedAt(new DateTime());
		$this->generateToken();
	}
	
	/** 
	 * @ORM\PreUpdate 
	 */  
	public function preUpdate()  
	{  
		$this->updatedAt = new DateTime();  
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
			'profile' => $this->getProfile(),
			'createdAt' => $this->getCreatedAt(),
			'updatedAt' => $this->getUpdatedAt(),
			'token' => $this->getToken(),
			'tokenCretedAt' => $this->getTokenCreatedAt()
		);
		return $data;
	}
}
