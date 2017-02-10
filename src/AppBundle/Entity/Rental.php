<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="RENTAL")
 */
class Rental {
	
	/**
	 * @ORM\Column(type="integer")
	 */
	private $user_id;
	
	/**
	 * @ORM\Column(type="integer")
	 */
	private $movie_id;
	
	/**
	 * @ORM\Column(type="integer")
         * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $rental_id;
	
	/**
	 * @ORM\Column(type="date")
	 */
	private $rental_initDate;
	
	/**
	 * @ORM\Column(type="date")
	 */
	private $rental_endDate;
	
	/**
	 * @ORM\Column(type="string", length=30)
	 */
	private $rental_status;
	

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Rental
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set movieId
     *
     * @param integer $movieId
     *
     * @return Rental
     */
    public function setMovieId($movieId)
    {
        $this->movie_id = $movieId;

        return $this;
    }

    /**
     * Get movieId
     *
     * @return integer
     */
    public function getMovieId()
    {
        return $this->movie_id;
    }

    /**
     * Set rentalId
     *
     * @param integer $rentalId
     *
     * @return Rental
     */
    public function setRentalId($rentalId)
    {
        $this->rental_id = $rentalId;

        return $this;
    }

    /**
     * Get rentalId
     *
     * @return integer
     */
    public function getRentalId()
    {
        return $this->rental_id;
    }

    /**
     * Set rentalInitDate
     *
     * @param \DateTime $rentalInitDate
     *
     * @return Rental
     */
    public function setRentalInitDate($rentalInitDate)
    {
        $this->rental_initDate = $rentalInitDate;

        return $this;
    }

    /**
     * Get rentalInitDate
     *
     * @return \DateTime
     */
    public function getRentalInitDate()
    {
        return $this->rental_initDate;
    }

    /**
     * Set rentalEndDate
     *
     * @param \DateTime $rentalEndDate
     *
     * @return Rental
     */
    public function setRentalEndDate($rentalEndDate)
    {
        $this->rental_endDate = $rentalEndDate;

        return $this;
    }

    /**
     * Get rentalEndDate
     *
     * @return \DateTime
     */
    public function getRentalEndDate()
    {
        return $this->rental_endDate;
    }

    /**
     * Set rentalStatus
     *
     * @param string $rentalStatus
     *
     * @return Rental
     */
    public function setRentalStatus($rentalStatus)
    {
        $this->rental_status = $rentalStatus;

        return $this;
    }

    /**
     * Get rentalStatus
     *
     * @return string
     */
    public function getRentalStatus()
    {
        return $this->rental_status;
    }
}
