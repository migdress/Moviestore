<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\User\UserInterface;

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
     * @ORM\Column(type="boolean")
     */
    private $user_isActive;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $user_type;

    //*******************************Beginning of functions************************************

    
    public function __construct(){
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
     * Set userType
     *
     * @param string $userType
     *
     * @return User
     */
    public function setUserType($userType) {
        $this->user_type = $userType;

        return $this;
    }

    /**
     * Get userType
     *
     * @return string
     */
    public function getUserType() {
        return $this->user_type;
    }
    
    /* Login method */
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

    public static function register($user_id, $user_name, $user_lastName, $user_login, $user_password, $user_type, EntityManager $em) {        
        $user = new User();
        $user->setUserId($user_id);
        $user->setUserName($user_name);
        $user->setUserLastName($user_lastName);
        $user->setUserLogin($user_login);
        $user->setUserPassword(sha1($user_password));
        $user->setUserType($user_type);
        User::registerToDB($user, $em);
        return 1;
    }
    
    public static function update($user_id, $user_name, $user_lastName, $user_login, $user_password, $user_type, EntityManager $em){
        $user = User::getTheUser($user_id, $em);
        $user->setUserName($user_name);
        $user->setUserLastName($user_lastName);
        $user->setUserLogin($user_login);
        if($user_password != ""){
            $user->setUserPassword(sha1($user_password));
        }
        $user->setUserType($user_type);
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
    
    
    /******************* These functions need to be declared to comply with the interface implementation*************/
    
    public function getRoles()
    {
        return array('ROLE_USER');
    }
    
    public function eraseCredentials(){
        
    }
    
    /** @see \Serializable::serialize()  */
    public function serialize(){
        return serialize([
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ]);
    }
    
    /** @see \Serializable::unserialize() */
    public function unserialize($serialized){
        list(
                $this->id,
                $this->user_username,
                $this->password,
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
}
