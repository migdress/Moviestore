<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;

/**
 * @ORM\Entity
 * @ORM\Table(name="USER")
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

    /* This variable will hold the entity manager */
    private $em;

    //*******************************Beginning of functions************************************
    
    public function __construct(EntityManager $em){
        $this->em = $em;
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

    /*Login method*/
    public function login($userLogin, $userPassword) {
        $userRepository = $this->em->getRepository("AppBundle:User");
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

}
