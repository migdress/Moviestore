<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
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

    /* Fetching all the genres */

    public static function getAllGenres(EntityManager $em) {
        $query = $em->createQuery('SELECT g FROM AppBundle:Genre g ORDER BY g.genre_name ASC');
        $genres = $query->getResult();
        if ($genres) {
            return $genres;
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

    /* Fetching one genre by Id */

    public static function getTheGenre($genreId, EntityManager $em) {
        $genresRepository = $em->getRepository("AppBundle:Genre");
        $genre = $genresRepository->find($genreId);
        if ($genre) {
            return $genre;
        } else {
            return null;
        }
    }

    /* Register Genre */

    public static function register($genre_name, EntityManager $em) {
        $genre = new Genre();
        $genre->setGenreName($genre_name);
        Genre::registerToDB($genre, $em);
        return 1;
    }

    /* Update Genre */

    public static function update($genre_id, $genre_name, EntityManager $em) {
        $genre = Genre::getTheGenre($genre_id, $em);
        $genre->setGenreName($genre_name);
        $em->flush();
        return 1;
    }

    /* Remove genre from database */

    public static function remove($genre_id, EntityManager $em) {
        $genre = Genre::getTheGenre($genre_id, $em);
        $em->remove($genre);
        $em->flush();
        return 1;
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
