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
    private $purchase_id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="purchases")
     * @ORM\JoinColumn(name="User_user_id", referencedColumnName="user_id")
     * @ORM\Id
     */
    private $user;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="Movie", inversedBy="purchases")
     * @ORM\JoinColumn(name="Movie_movie_id", referencedColumnName="movie_id") 
     * @ORM\Id
     */
    private $movie;
    
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
     * Set purchaseDate
     *
     * @param \DateTime $purchaseDate
     *
     * @return Purchase
     */
    public function setPurchaseDate($purchaseDate) {
        $this->purchase_date = $purchaseDate;

        return $this;
    }

    /**
     * Get purchaseDate
     *
     * @return \DateTime
     */
    public function getPurchaseDate() {
        return $this->purchase_date;
    }


    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Purchase
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
     * Set movie
     *
     * @param \AppBundle\Entity\Movie $movie
     *
     * @return Purchase
     */
    public function setMovie(\AppBundle\Entity\Movie $movie = null)
    {
        $this->movie = $movie;

        return $this;
    }

    /**
     * Get movie
     *
     * @return \AppBundle\Entity\Movie
     */
    public function getMovie()
    {
        return $this->movie;
    }

    /**
     * Set purchaseId
     *
     * @param integer $purchaseId
     *
     * @return Purchase
     */
    public function setPurchaseId($purchaseId)
    {
        $this->purchase_id = $purchaseId;

        return $this;
    }

    /**
     * Get purchaseId
     *
     * @return integer
     */
    public function getPurchaseId()
    {
        return $this->purchase_id;
    }
}
