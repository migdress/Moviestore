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

    /* This is not a property of the table in the DB, is an attribute whose function
        is hold the role strings */
    private $user_roles = array();

    //*******************************Beginning of functions************************************

    
    public function __construct(){
        $this->products = new ArrayCollection();
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

    
    
    /* Old login method, Previous to the symfony security system */
    public static function login($userLogin, $userPassword, EntityManager $em) {
        $userRepository = $em->getRepository("AppBundle:User");
        /* Trying to find the User */
        if ($user = $userRepository->findOneBy(array(
            "user_username" => $userLogin
                ))) {
            if ($user->getPassword() == sha1($userPassword)) {
                $return = $user;
                return $return;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /* Get all users method */

    public static function getAllUsers(EntityManager $em) {
        $usersRepository = $em->getRepository("AppBundle:User");
        $users = $usersRepository->findAll();
        if ($users) {
            return $users;
        } else {
            return null;
        }
    }

    public static function register($user_id, $user_name, $user_lastName, $user_login, $user_password, $user_email, $user_type, EntityManager $em) {        
        $user = new User();
        $user->setUserId($user_id);
        $user->setUserName($user_name);
        $user->setUserLastName($user_lastName);
        $user->setUserUsername($user_login);
        $user->setUserPassword(sha1($user_password));
        $user->setUserEmail($user_email);
        $user->setUserIsActive(true);
        User::registerToDB($user, $em);
        $userHasRole = new User_has_Role();
        $userHasRole->setRole(Role::getTheRole($user_type, $em));
        $userHasRole->setUser($user);
        $user->addUserHasRole($userHasRole);
        $em->flush();
        return 1;
    }
    
    public static function update($user_id, $user_name, $user_lastName, $user_login, $user_password, $user_email, $user_type, EntityManager $em){
        $user = User::getTheUser($user_id, $em);
        $user->setUserName($user_name);
        $user->setUserLastName($user_lastName);
        $user->setUserUsername($user_login);
        if($user_password != ""){
            $user->setUserPassword(sha1($user_password));
        }
        $user->setUserEmail($user_email);
        $currentRole = $user->getUserHasRoles();
        if (!$currentRole[0]->getRole()->getRoleId() == $user_type) {
            User_has_Role::removeRecords($user_id, $em);
            $userHasRole = new User_has_Role();
            $userHasRole->setRole(Role::getTheRole($user_type, $em));
            $userHasRole->setUser($user);
            $user->addUserHasRole($userHasRole);
        }
        $em->flush();
        return 1;
    }

    /* Register any object to DB */
    public static function registerToDB($object, EntityManager $em) {
        try {
            $em->persist($object);
            $em->flush();
        } catch (Exeption $e) {
            return false;
        }
        return true;
    }
    
    /*Fetching one user by Id*/
    public static function getTheUser($userId, EntityManager $em) {
        $userRepository = $em->getRepository("AppBundle:User");
        $user = $userRepository->find($userId);
        if ($user) {
            return $user;
        } else {
            return null;
        }
    }
    
    /*Remove user from database*/
    public static function remove($user_id, EntityManager $em){
        $user = User::getTheUser($user_id, $em);
        $em->remove($user);
        $em->flush();
        return 1;
    }
    
    public function loadRoles(EntityManager $em){
        $userRoles = User_has_Role::getTheRoles($this->user_id, $em);
        if($userRoles){
            $i = 0;
            foreach($userRoles as $userRole){
                $this->user_roles[$i] = $userRole->getRoleString();
                $i++;
            }
        }
    }
    
    /*This function has to be called after calling loadRoles()*/
    public function hasTheRole($ROLE_STRING){
        if ($this->user_roles != null) {
            foreach ($this->user_roles as $role) {
                if ($role == $ROLE_STRING) {
                    return true;
                }
            }
        } else {
            return false;
        }
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
