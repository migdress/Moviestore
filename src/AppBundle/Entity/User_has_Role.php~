<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;
use AppBundle\Utils\Constants;
use AppBundle\Entity\User;
use AppBundle\Entity\Movie;
use AppBundle\Entity\Genre;

/**
 * @ORM\Entity
 * @ORM\Table(name="User_has_Role")
 */
class User_has_Role {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $User_user_id;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $Role_role_id;
    
    /**********************Beginning of functions*****************************/

    /**
     * Set userUserId
     *
     * @param integer $userUserId
     *
     * @return User_has_Role
     */
    public function setUserUserId($userUserId) {
        $this->User_user_id = $userUserId;

        return $this;
    }

    /**
     * Get userUserId
     *
     * @return integer
     */
    public function getUserUserId() {
        return $this->User_user_id;
    }

    /**
     * Set roleRoleId
     *
     * @param integer $roleRoleId
     *
     * @return User_has_Role
     */
    public function setRoleRoleId($roleRoleId) {
        $this->Role_role_id = $roleRoleId;

        return $this;
    }

    /**
     * Get roleRoleId
     *
     * @return integer
     */
    public function getRoleRoleId() {
        return $this->Role_role_id;
    }

    /* Fetching roles for a given user Id */
    public static function getTheRoles($userId, EntityManager $em) {
        $repository = $em->getRepository("AppBundle:User_has_Role");
        $result = $repository->findBy(["User_user_id" => $userId]);
        if ($result) {
            $userRoles = array();
            $i = 0;
            foreach ($result as $record) {
                $userRoles[$i] = Role::getTheRole($record->getRoleRoleId(), $em);
                $i++;
            }
            return $userRoles;
        } else {
            return null;
        }
    }
    
    public static function getTheUser_has_RoleRecords($userId, EntityManager $em) {
        $repository = $em->getRepository("AppBundle:User_has_Role");
        $records = $repository->findBy(["User_user_id" => $userId]);
        if ($records) {
            return $records;
        } else {
            return null;
        }
    }

    /* Registering new relationships between Movie and Genre */
    public static function registerRecords($userId, Array $userRoleIds = null, EntityManager $em) {
        if ($userRolesIds) {
            foreach ($userRoleIds as $userRoleId) {
                if ($userRoleId) {
                    $userHasRole = new User_has_Role();
                    $userHasRole->setUserUserId($userId);
                    $userHasRole->setRoleRoleId($userRoleId);
                    User_has_Role::registerToDB($userHasRole, $em);
                }
            }
        }else{
            return null;
        }
        return 1;
    }

    /* Updating relationships between User and Role */
    public static function updateRecords($userId, Array $userRoleIds = null, EntityManager $em) {
        //Deleting actual records
        User_has_Role::removeRecords($userId, $em);
        //Registering new records
        User_has_Role::registerRecords($userId, $userRoleIds, $em);
        return 1;
    }

    public static function removeRecords($user_id, EntityManager $em) {
        $currentRecords = User_has_Role::getTheUser_has_RoleRecords($user_id, $em);
        if ($currentRecords) {
            foreach ($currentRecords as $currentRecord) {
                $em->remove($currentRecord);
            }
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

}
