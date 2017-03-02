<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;


/**
 * @ORM\Entity
 * @ORM\Table(name="Purchase")
 */
class Purchase {
	
	/**
	 * @ORM\Column(type="integer")
         * @ORM\Id
	 */
	private $User_user_id;
	
	/**
	 * @ORM\Column(type="integer")
         * @ORM\Id
	 */
	private $Movie_movie_id;
	
	/**
	 * @ORM\Column(type="date")
	 */
	private $purchase_date;
	
	

    
    /* Fetching all rentals */
    public static function getAllPurchases(EntityManager $em) {
        $rentalsRepository = $em->getRepository("AppBundle:Purchase");
        $rentals = $rentalsRepository->findAll();
        if ($rentals) {
            return $rentals;
        } else {
            return null;
        }
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
     * Set userUserId
     *
     * @param integer $userUserId
     *
     * @return Purchase
     */
    public function setUserUserId($userUserId)
    {
        $this->User_user_id = $userUserId;

        return $this;
    }

    /**
     * Get userUserId
     *
     * @return integer
     */
    public function getUserUserId()
    {
        return $this->User_user_id;
    }

    /**
     * Set movieMovieId
     *
     * @param integer $movieMovieId
     *
     * @return Purchase
     */
    public function setMovieMovieId($movieMovieId)
    {
        $this->Movie_movie_id = $movieMovieId;

        return $this;
    }

    /**
     * Get movieMovieId
     *
     * @return integer
     */
    public function getMovieMovieId()
    {
        return $this->Movie_movie_id;
    }

    /**
     * Set purchaseDate
     *
     * @param \DateTime $purchaseDate
     *
     * @return Purchase
     */
    public function setPurchaseDate($purchaseDate)
    {
        $this->purchase_date = $purchaseDate;

        return $this;
    }

    /**
     * Get purchaseDate
     *
     * @return \DateTime
     */
    public function getPurchaseDate()
    {
        return $this->purchase_date;
    }
}
