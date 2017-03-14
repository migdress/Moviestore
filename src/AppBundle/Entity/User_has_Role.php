<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;
use AppBundle\Utils\Constants;
use AppBundle\Entity\User;
use AppBundle\Entity\Movie;
use AppBundle\Entity\Genre;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\User_has_RoleRepository")
 * @ORM\Table(name="User_has_Role")
 */
class User_has_Role {

    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="user_has_roles")
     * @ORM\JoinColumn(name="User_user_id", referencedColumnName="user_id")
     * @ORM\Id
     */
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="user_has_roles")
     * @ORM\JoinColumn(name="Role_role_id", referencedColumnName="role_id")
     * @ORM\Id
     */
    private $role;
    
    /**********************Beginning of functions*****************************/

    /* Fetching roles for a given user Id */
    public static function getTheRoles($userId, EntityManager $em) {
        $repository = $em->getRepository("AppBundle:User_has_Role");
        //$result = $repository->findBy(["User_user_id" => $userId]);
        $result = $repository->findBy(["user" => $userId]);
        if ($result) {
            $userRoles = array();
            $i = 0;
            foreach ($result as $record) {
                $userRoles[$i] = Role::getTheRole($record->getRole(), $em);
                $i++;
            }
            return $userRoles;
        } else {
            return null;
        }
    }
    
    public static function getTheUser_has_RoleRecords($userId, EntityManager $em) {
        $repository = $em->getRepository("AppBundle:User_has_Role");
        $records = $repository->findBy(["user" => $userId]);
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

    public static function removeRecords($user_id, EntityManager $em, $logger = null)  {
        $currentRecords = User_has_Role::getTheUser_has_RoleRecords($user_id, $em);
        if($logger!=null){$logger->info("METHOD: 'User_has_Role.removeRecords => CALLED");}
        if ($currentRecords) {
            foreach ($currentRecords as $currentRecord) {
                if($logger){$logger->info("METHOD: 'User_has_Role.removeRecords => Found userId: ".$currentRecord->getUser()->getUserId()." Found RoleId: ".$currentRecord->getRole()->getRoleId());}
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


    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return User_has_Role
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set role
     *
     * @param \AppBundle\Entity\Role $role
     *
     * @return User_has_Role
     */
    public function setRole(\AppBundle\Entity\Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \AppBundle\Entity\Role
     */
    public function getRole()
    {
        return $this->role;
    }
}