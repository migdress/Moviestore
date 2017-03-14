<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\User\UserInterface;
use AppBundle\Utils\Constants;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\User_has_Role;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="User")
 */
class User implements UserInterface, \Serializable{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $user_id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $user_name;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $user_lastname;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $user_username;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $user_password;
    
    /**
     * @ORM\Column(type="string", length=45)
     */
    private $user_email;
    
    /**
     * 
     * @ORM\OneToMany(targetEntity="Purchase", mappedBy="user")
     */
    private $purchases;
    
    /**
     * The cascade=persist attribute below forces this entity to persist the changes to
     * the entity with the association ORM\OneToMany
     * 
     * @ORM\OneToMany(targetEntity="User_has_Role", mappedBy="user", cascade={"persist"})
     */
    private $user_has_roles;
    
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $user_isActive;

    //*******************************Beginning of functions************************************

    
    public function __construct(){
        $this->purchases = new ArrayCollection();
        $this->user_has_roles = new ArrayCollection();
        $this->isActive = true;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid(null, true));
    }
    
    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return User
     */
    public function setUserId($userId) {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId() {
        return $this->user_id;
    }

    /**
     * Set userName
     *
     * @param string $userName
     *
     * @return User
     */
    public function setUserName($userName) {
        $this->user_name = $userName;

        return $this;
    }

    /**
     * Get userName
     *
     * @return string
     */
    public function getUserName() {
        return $this->user_name;
    }

    /**
     * Set userLastname
     *
     * @param string $userLastname
     *
     * @return User
     */
    public function setUserLastname($userLastname) {
        $this->user_lastname = $userLastname;

        return $this;
    }

    /**
     * Get userLastname
     *
     * @return string
     */
    public function getUserLastname() {
        return $this->user_lastname;
    }

    /**
     * Set userUsername
     *
     * @param string $userUsername
     *
     * @return User
     */
    public function setUserUsername($userUsername) {
        $this->user_username = $userUsername;

        return $this;
    }

    /**
     * Get userUsername
     *
     * @return string
     */
    public function getUserUsername() {
        return $this->user_username;
    }

    /**
     * Set userPassword
     *
     * @param string $userPassword
     *
     * @return User
     */
    public function setUserPassword($userPassword) {
        $this->user_password = $userPassword;

        return $this;
    }

    /**
     * Get userPassword
     *
     * @return string
     */
    public function getUserPassword() {
        return $this->user_password;
    }
    
    /**
     * Get userRoles
     *
     * @return string
     */
    public function getUserRoles() {
        return implode(",",$this->getRoles());
    }
    
    /******************* These functions need to be declared to comply with the interface implementation*************/
    
    public function getRoles()
    {
        $rolesArray = array();
        $i = 0;
        foreach($this->user_has_roles as $user_has_role){
            $rolesArray[$i] = $user_has_role->getRole()->getRoleString();
        }
        
        return $rolesArray;
    }
    
    public function hasTheRole($roleString, $logger = null){
        $rolesArray = $this->getRoles();
        foreach($rolesArray as $role){
            if($logger!=null){$logger->info("METHOD: 'User.hasTheRole' => asking for: ".$roleString." got: ".$role);}
            if($roleString == $role){
                return true;
            }
        }
        return false;
    }
    
    public function eraseCredentials(){
        
    }
    
    /*
     * IMPORTANT: When retrieving the user object in controllers via 
     * security system, the user object will be able to use only the 
     * attributes which have been serialized, so keep that in mind when
     * serializing attributes below 
     */
    
    /** @see \Serializable::serialize()  */
    public function serialize(){
        return serialize([
            $this->user_id,
            $this->user_username,
            $this->user_password,
            // see section on salt below
            // $this->salt,
        ]);
    }
    
    /** @see \Serializable::unserialize() */
    public function unserialize($serialized) {
        list(
                $this->user_id,
                $this->user_username,
                $this->user_password,
                // see section on salt below
                // $this->salt
                ) = unserialize($serialized);
    }

    public function getPassword(){
        return $this->getUserPassword();
    }
    
    public function getSalt(){
        /* You *may* need a real salt depending on your encoder */
        /* If the encoder is bcrypt, there is not problem with this method returning null */
        return null;
    }

    /**
     * Set userEmail
     *
     * @param string $userEmail
     *
     * @return User
     */
    public function setUserEmail($userEmail)
    {
        $this->user_email = $userEmail;

        return $this;
    }

    /**
     * Get userEmail
     *
     * @return string
     */
    public function getUserEmail()
    {
        return $this->user_email;
    }

    /**
     * Set userIsActive
     *
     * @param boolean $userIsActive
     *
     * @return User
     */
    public function setUserIsActive($userIsActive)
    {
        $this->user_isActive = $userIsActive;

        return $this;
    }

    /**
     * Get userIsActive
     *
     * @return boolean
     */
    public function getUserIsActive()
    {
        return $this->user_isActive;
    }

    /**
     * Add purchase
     *
     * @param \AppBundle\Entity\Purchase $purchase
     *
     * @return User
     */
    public function addPurchase(\AppBundle\Entity\Purchase $purchase)
    {
        $this->purchases[] = $purchase;

        return $this;
    }

    /**
     * Remove purchase
     *
     * @param \AppBundle\Entity\Purchase $purchase
     */
    public function removePurchase(\AppBundle\Entity\Purchase $purchase)
    {
        $this->purchases->removeElement($purchase);
    }

    /**
     * Get purchases
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPurchases()
    {
        return $this->purchases;
    }

    /**
     * Add userHasRole
     *
     * @param \AppBundle\Entity\User_has_Role $userHasRole
     *
     * @return User
     */
    public function addUserHasRole(\AppBundle\Entity\User_has_Role $userHasRole)
    {
        $this->user_has_roles[] = $userHasRole;

        return $this;
    }

    /**
     * Remove userHasRole
     *
     * @param \AppBundle\Entity\User_has_Role $userHasRole
     */
    public function removeUserHasRole(\AppBundle\Entity\User_has_Role $userHasRole)
    {
        $this->user_has_roles->removeElement($userHasRole);
    }

    /**
     * Get userHasRoles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserHasRoles()
    {
        return $this->user_has_roles;
    }
}
