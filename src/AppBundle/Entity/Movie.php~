<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="movie")
 */
class Movie {
	
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $movie_id;
	
	/**
	 * @ORM\Column(type="integer")
	 * 
	 */
	private $genre_id;
	
	/**
	 * @ORM\Column(type="string",length=30)
	 */
	private $movie_name;
	
	/**
	 * @ORM\Column(type="string",length=150)
	 */
	private $movie_desc;
	

    /**
     * Set movieId
     *
     * @param integer $movieId
     *
     * @return Movie
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
     * Set genreId
     *
     * @param integer $genreId
     *
     * @return Movie
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
     * Set movieName
     *
     * @param string $movieName
     *
     * @return Movie
     */
    public function setMovieName($movieName)
    {
        $this->movie_name = $movieName;

        return $this;
    }

    /**
     * Get movieName
     *
     * @return string
     */
    public function getMovieName()
    {
        return $this->movie_name;
    }

    /**
     * Set movieDesc
     *
     * @param string $movieDesc
     *
     * @return Movie
     */
    public function setMovieDesc($movieDesc)
    {
        $this->movie_desc = $movieDesc;

        return $this;
    }

    /**
     * Get movieDesc
     *
     * @return string
     */
    public function getMovieDesc()
    {
        return $this->movie_desc;
    }
}
