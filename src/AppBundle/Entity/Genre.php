<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GenreRepository")
 * @ORM\Table(name="Genre")
 */
class Genre {

    /**
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     */
    private $genre_id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $genre_name;
    
    /**
     * 
     * @ORM\OneToMany(targetEntity="Movie_has_Genre", mappedBy="genre")
     */
    private $movie_has_genres;
    
    
    /************************ Beginning of functions **********************/
    
    public function __construct(){
        $this->movie_has_genres = new ArrayCollection();
    }

    /**
     * Set genreId
     *
     * @param integer $genreId
     *
     * @return Genre
     */
    public function setGenreId($genreId) {
        $this->genre_id = $genreId;

        return $this;
    }

    /**
     * Get genreId
     *
     * @return integer
     */
    public function getGenreId() {
        return $this->genre_id;
    }

    /**
     * Set genreName
     *
     * @param string $genreName
     *
     * @return Genre
     */
    public function setGenreName($genreName) {
        $this->genre_name = $genreName;

        return $this;
    }

    /**
     * Get genreName
     *
     * @return string
     */
    public function getGenreName() {
        return $this->genre_name;
    }



    /**
     * Add movieHasGenre
     *
     * @param \AppBundle\Entity\Movie_has_Genre $movieHasGenre
     *
     * @return Genre
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
