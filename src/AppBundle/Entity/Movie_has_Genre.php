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
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $Movie_movie_id;
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $Genre_genre_id;



    /**
     * Set movieMovieId
     *
     * @param integer $movieMovieId
     *
     * @return Movie_has_Genre
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
     * Set genreGenreId
     *
     * @param integer $genreGenreId
     *
     * @return Movie_has_Genre
     */
    public function setGenreGenreId($genreGenreId)
    {
        $this->Genre_genre_id = $genreGenreId;

        return $this;
    }

    /**
     * Get genreGenreId
     *
     * @return integer
     */
    public function getGenreGenreId()
    {
        return $this->Genre_genre_id;
    }
    
    /*Fetching genres for a given movie Id*/
    public static function getTheGenres($movieId, EntityManager $em) {
        $repository = $em->getRepository("AppBundle:Movie_has_Genre");
        $result = $repository->findBy(["Movie_movie_id" => $movieId]);
        if ($result) {
            $movieGenres = array();
            $i = 0;
            foreach($result as $record){
                $movieGenres[$i] = Genre::getTheGenre($record->getGenreGenreId(), $em);
                $i++;
            }
            return $movieGenres;
        } else {
            return null;
        }
    }
}
