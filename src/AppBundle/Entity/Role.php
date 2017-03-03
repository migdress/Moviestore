<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;

/**
 * @ORM\Entity
 * @ORM\Table(name="Role")
 */
class Role {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $role_id;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $role_string;
    
    /*************************************** Begninning of functions *********************************/

    /**
     * Set roleId
     *
     * @param integer $roleId
     *
     * @return Role
     */
    public function setRoleId($roleId) {
        $this->role_id = $roleId;

        return $this;
    }

    /**
     * Get roleId
     *
     * @return integer
     */
    public function getRoleId() {
        return $this->role_id;
    }

    /**
     * Set roleString
     *
     * @param string $roleString
     *
     * @return Role
     */
    public function setRoleString($roleString) {
        $this->role_string = $roleString;

        return $this;
    }

    /**
     * Get roleString
     *
     * @return string
     */
    public function getRoleString() {
        return $this->role_string;
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

    /* Fetching one role by Id */
    public static function getTheRole($roleId, EntityManager $em) {
        $rolesRepository = $em->getRepository("AppBundle:Role");
        $role = $rolesRepository->find($roleId);
        if ($role) {
            return $role;
        } else {
            return null;
        }
    }
}
