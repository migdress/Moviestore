<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;

/**
 * @ORM\Entity
 * @ORM\Table(name="User")
 */
class User {

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
    private $user_lastName;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $user_login;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $user_password;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $user_type;

    //*******************************Beginning of functions************************************

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
     * Set userLastName
     *
     * @param string $userLastName
     *
     * @return User
     */
    public function setUserLastName($userLastName) {
        $this->user_lastName = $userLastName;

        return $this;
    }

    /**
     * Get userLastName
     *
     * @return string
     */
    public function getUserLastName() {
        return $this->user_lastName;
    }

    /**
     * Set userLogin
     *
     * @param string $userLogin
     *
     * @return User
     */
    public function setUserLogin($userLogin) {
        $this->user_login = $userLogin;

        return $this;
    }

    /**
     * Get userLogin
     *
     * @return string
     */
    public function getUserLogin() {
        return $this->user_login;
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
            "user_login" => $userLogin
                ))) {
            if ($user->getUserPassword() == sha1($userPassword)) {
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

}
