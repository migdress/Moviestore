<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="genre")
 */
class Genre {
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 */
	private $genre_id;
	
	/**
	 * @ORM\Column(type="string", length=30)
	 */
	private $genre_name;
	

    /**
     * Set genreId
     *
     * @param integer $genreId
     *
     * @return Genre
     */
    public function setGenreId($genreId)
    {
        $this->genre_id = $genreId;

        return $this;
    }

    /**
     * Get genreId
     *
     * @return integer
     */
    public function getGenreId()
    {
        return $this->genre_id;
    }

    /**
     * Set genreName
     *
     * @param string $genreName
     *
     * @return Genre
     */
    public function setGenreName($genreName)
    {
        $this->genre_name = $genreName;

        return $this;
    }

    /**
     * Get genreName
     *
     * @return string
     */
    public function getGenreName()
    {
        return $this->genre_name;
    }
}
