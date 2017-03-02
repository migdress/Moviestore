<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Movie_has_Genre;

/**
 * @ORM\Entity
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
        
        private $movie_genres = array();
        
    
    
	

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
    
    /*Register Movie*/
public static function register(Array $movie_genres, $movie_name, $movie_price, $imageName, $movie_desc, EntityManager $em){
        $movie = new Movie();
        $movie->setMovieName($movie_name);
        $movie->setMoviePrice($movie_price);
        $movie->setMovieDesc($movie_desc);
        Movie::registerToDB($movie, $em);
        Movie_has_Genre::registerRecords($movie->getMovieId(), $movie_genres, $em);
        if ($imageName != null) {
            $movie->setMovieImagePath($imageName);
        }
        $em->flush();
        return $movie->getMovieId();
    }
    
    public static function update($movie_id, Array $movie_genres = null, $movie_name, $movie_price, $imageName, $movie_desc, EntityManager $em){
        $movie = Movie::getTheMovie($movie_id, $em);
        $movie->setMovieName($movie_name);
        $movie->setMoviePrice($movie_price);
        $movie->setMovieDesc($movie_desc);
        if ($imageName != null) {
            $movie->setMovieImagePath($imageName);
        }
        $em->flush();
        Movie_has_Genre::updateRecords($movie_id, $movie_genres, $em);
        return $movie->getMovieId();
    }
    
    public static function remove($movie_id, EntityManager $em){
        $movie = Movie::getTheMovie($movie_id, $em);
        Movie_has_Genre::removeRecords($movie_id, $em);
        $em->remove($movie);
        $em->flush();
        return 1;
    }
    
    
    /* Fetching all the movies */
    public static function getAllMovies(EntityManager $em) {
        $moviesRepository = $em->getRepository("AppBundle:Movie");
        $query = $em->createQuery('SELECT m FROM AppBundle:Movie m ORDER BY m.movie_name ASC');
        //$movies = $moviesRepository->findAll();
        $movies = $query->getResult();
        if ($movies) {
            foreach($movies as $movie){
                $movie->loadGenres($em);
            }
            return $movies;
        } else {
            return null;
        }
    }
    
    /*Fetching one movie by Id*/
    public static function getTheMovie($movieId, EntityManager $em) {
        $moviesRepository = $em->getRepository("AppBundle:Movie");
        $movie = $moviesRepository->find($movieId);
        if ($movie) {
            $movie->loadGenres($em);
            return $movie;
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
     * Set moviePrice
     *
     * @param integer $moviePrice
     *
     * @return Movie
     */
    public function setMoviePrice($moviePrice)
    {
        $this->movie_price = $moviePrice;

        return $this;
    }

    /**
     * Get moviePrice
     *
     * @return integer
     */
    public function getMoviePrice()
    {
        return $this->movie_price;
    }

    /**
     * Set movieImagePath
     *
     * @param string $movieImagePath
     *
     * @return Movie
     */
    public function setMovieImagePath($movieImagePath)
    {
        $this->movie_imagePath = $movieImagePath;

        return $this;
    }

    /**
     * Get movieImagePath
     *
     * @return string
     */
    public function getMovieImagePath()
    {
        return $this->movie_imagePath;
    }
    
    /**
     * Get movieGenres
     *
     * @return array
     */
    public function getMovieGenres()
    {
        return $this->movie_genres;
    } 
    
    /*This function loads all the genres belonging to the movie*/
    public function loadGenres(EntityManager $em){
        $this->movie_genres = Movie_has_Genre::getTheGenres($this->movie_id, $em);
    }
}
