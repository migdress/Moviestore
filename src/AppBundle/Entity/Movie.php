<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Movie_has_Genre;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Monolog\Logger;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MovieRepository")
 * @ORM\Table(name="Movie")
 */
class Movie {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $movie_id;

    /**
     * @ORM\Column(type="string",length=30)
     */
    private $movie_name;

    /**
     * @ORM\Column(type="string",length=150)
     */
    private $movie_desc;

    /**
     * @ORM\Column(type="integer")
     */
    private $movie_price;

    /**
     * @ORM\Column(type="string", length=30, nullable=TRUE)
     *
     */
    private $movie_imagePath;

    /**
     * 
     * @ORM\OneToMany(targetEntity="Purchase", mappedBy="movie", cascade={"Persist"})
     */
    private $purchases;
    
    /**
     * 
     * @ORM\OneToMany(targetEntity="Movie_has_Genre", mappedBy="movie", cascade={"persist"})
     */
    private $movie_has_genres;
    
    
    /**********************Beginning of functions****************************/
    public function __construct() {
        $this->purchases = new ArrayCollection();
        $this->movie_has_genres = new ArrayCollection();
        $logger = new Logger();
    }

    /**
     * Set movieId
     *
     * @param integer $movieId
     *
     * @return Movie
     */
    public function setMovieId($movieId) {
        $this->movie_id = $movieId;

        return $this;
    }

    /**
     * Get movieId
     *
     * @return integer
     */
    public function getMovieId() {
        return $this->movie_id;
    }

    /**
     * Set movieName
     *
     * @param string $movieName
     *
     * @return Movie
     */
    public function setMovieName($movieName) {
        $this->movie_name = $movieName;

        return $this;
    }

    /**
     * Get movieName
     *
     * @return string
     */
    public function getMovieName() {
        return $this->movie_name;
    }

    /**
     * Set movieDesc
     *
     * @param string $movieDesc
     *
     * @return Movie
     */
    public function setMovieDesc($movieDesc) {
        $this->movie_desc = $movieDesc;

        return $this;
    }

    /**
     * Get movieDesc
     *
     * @return string
     */
    public function getMovieDesc() {
        return $this->movie_desc;
    }


    /**
     * Set moviePrice
     *
     * @param integer $moviePrice
     *
     * @return Movie
     */
    public function setMoviePrice($moviePrice) {
        $this->movie_price = $moviePrice;

        return $this;
    }

    /**
     * Get moviePrice
     *
     * @return integer
     */
    public function getMoviePrice() {
        return $this->movie_price;
    }

    /**
     * Set movieImagePath
     *
     * @param string $movieImagePath
     *
     * @return Movie
     */
    public function setMovieImagePath($movieImagePath) {
        $this->movie_imagePath = $movieImagePath;

        return $this;
    }

    /**
     * Get movieImagePath
     *
     * @return string
     */
    public function getMovieImagePath() {
        return $this->movie_imagePath;
    }

    /**
     * Get movieGenres
     *
     * @return array
     */
    public function getMovieGenres() {
        $genres = array();
        $i = 0;
        foreach ($this->getMovieHasGenres() as $movie_has_genre) {
            $genres[$i] = $movie_has_genre->getGenre();
            $i++;
        }
        return $genres;
    }

    /**
     * Add purchase
     *
     * @param \AppBundle\Entity\Purchase $purchase
     *
     * @return Movie
     */
    public function addPurchase(\AppBundle\Entity\Purchase $purchase)
    {
        $this->purchases[] = $purchase;

        return $this;
    }

    /**
     * Remove purchase
     *
     * @param \AppBundle\Entity\Purchase $purchase
     */
    public function removePurchase(\AppBundle\Entity\Purchase $purchase)
    {
        $this->purchases->removeElement($purchase);
    }

    /**
     * Get purchases
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPurchases()
    {
        return $this->purchases;
    }

    /**
     * Add movieHasGenre
     *
     * @param \AppBundle\Entity\Movie_has_Genre $movieHasGenre
     *
     * @return Movie
     */
    public function addMovieHasGenre(\AppBundle\Entity\Movie_has_Genre $movieHasGenre)
    {
        $this->movie_has_genres[] = $movieHasGenre;

        return $this;
    }

    /**
     * Remove movieHasGenre
     *
     * @param \AppBundle\Entity\Movie_has_Genre $movieHasGenre
     */
    public function removeMovieHasGenre(\AppBundle\Entity\Movie_has_Genre $movieHasGenre)
    {
        $this->movie_has_genres->removeElement($movieHasGenre);
    }

    /**
     * Get movieHasGenres
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMovieHasGenres()
    {
        return $this->movie_has_genres;
    }
}
