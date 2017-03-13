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
 * @ORM\Table(name="Movie_has_Genre")
 */
class Movie_has_Genre {
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="Movie", inversedBy="movie_has_genres")
     * @ORM\JoinColumn(name="Movie_movie_id", referencedColumnName="movie_id")
     * @ORM\Id 
     */
    private $movie;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="Genre", inversedBy="movie_has_genres")
     * @ORM\JoinColumn(name="Genre_genre_id", referencedColumnName="genre_id")
     * @ORM\Id 
     */
    private $genre;
    
   
/**************************************Beginning of functions **********************************/

    /* Fetching genres for a given movie Id */

    public static function getTheGenres($movieId, EntityManager $em) {
        $repository = $em->getRepository("AppBundle:Movie_has_Genre");
        $result = $repository->findBy(["Movie_movie_id" => $movieId]);
        if ($result) {
            $movieGenres = array();
            $i = 0;
            foreach ($result as $record) {
                $movieGenres[$i] = Genre::getTheGenre($record->getGenreGenreId(), $em);
                $i++;
            }
            return $movieGenres;
        } else {
            return null;
        }
    }

    public static function getTheMovie_has_GenreRecords($movieId, EntityManager $em) {
        $repository = $em->getRepository("AppBundle:Movie_has_Genre");
        $records = $repository->findBy(["Movie_movie_id" => $movieId]);
        if ($records) {
            return $records;
        } else {
            return null;
        }
    }

    /* Registering new relationships between Movie and Genre */

    public static function registerRecords($movieId, Array $movie_genres = null, EntityManager $em) {
        if ($movie_genres) {
            foreach ($movie_genres as $movie_genre) {
                if ($movie_genre) {
                    $movieHG = new Movie_has_Genre();
                    $movieHG->setMovieMovieId($movieId);
                    $movieHG->setGenreGenreId($movie_genre);
                    Movie_has_Genre::registerToDB($movieHG, $em);
                }
            }
        }
        return 1;
    }

    /* Updating relationships between Movie and Genre */

    public static function updateRecords($movieId, Array $movie_genres = null, EntityManager $em) {
        //Deleting actual records
        Movie_has_Genre::removeRecords($movieId, $em);
        //Registering new records
        Movie_has_Genre::registerRecords($movieId, $movie_genres, $em);
        return 1;
    }

    public static function removeRecords($movie_id, EntityManager $em) {
        $actualGenres = Movie_has_Genre::getTheMovie_has_GenreRecords($movie_id, $em);
        if ($actualGenres) {
            foreach ($actualGenres as $actualGenre) {
                $em->remove($actualGenre);
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
     * Set movie
     *
     * @param \AppBundle\Entity\Movie $movie
     *
     * @return Movie_has_Genre
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
     * Set genre
     *
     * @param \AppBundle\Entity\Genre $genre
     *
     * @return Movie_has_Genre
     */
    public function setGenre(\AppBundle\Entity\Genre $genre = null)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get genre
     *
     * @return \AppBundle\Entity\Genre
     */
    public function getGenre()
    {
        return $this->genre;
    }
}
