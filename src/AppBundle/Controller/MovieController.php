<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Entity\Movie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class MovieController extends Controller {
	
	/**
	 * @Route("/manageMovies", name="manageMovies")
	 */
	public function manageMoviesAction(Request $request) {
		$user = $request->getSession ()->get ( "user" );
		if ($user->getUserType () == "ADMIN") {
			$movies = $this->getAllMovies ();
			$genres = $this->getAllGenres ();
			$rentals = $this->getAllRentals ();
			return $this->render ( "manageMovies.html.twig", array (
					"user" => $user,
					"movies" => $movies,
					"genres" => $genres,
					"rentals" => $rentals 
			) );
		} else {
			$this->addFlash ( "error", "You are not allowed to be here!" );
			return $this->render ( "accout.html.twig" );
		}
	}
	
	/**
	 * @Route("/registerMovie", name="registerMovie")
	 */
	public function registerMovieAction(Request $request) {
		$user = $request->getSession ()->get ( "user" );
		$movie = new Movie ();
		$movie->setGenreId ( null );
		$movie->setMovieName ( $request->request->get ( "registerMovieName" ) );
		$movie->setGenreId ( $request->request->get ( "registerMovieGenre" ) );
		$movie->setMovieDesc ( $request->request->get ( "registerMovieDesc" ) );
		$this->registerToDB ( $movie );
		$this->addFlash ( "notice", "The movie " . $movie->getMovieName () . " has been registered succesfuly" );
		
		$movies = $this->getAllMovies ();
		$genres = $this->getAllGenres ();
		$rentals = $this->getAllRentals ();
		return $this->render ( "manageMovies.html.twig", array (
				"user" => $user,
				"movies" => $movies,
				"genres" => $genres,
				"rentals" => $rentals 
		) );
	}
	
	/**
	 * @Route("/editMovie/{movieId}", name="editMovie", requirements={"movieId": "\d+"})
	 */
	public function editMovieAction(Request $request, $movieId) {
		$user = $request->getSession ()->get ( "user" );
		$movie = $this->getTheMovie ( $movieId );
		
		$movies = $this->getAllMovies ();
		$genres = $this->getAllGenres ();
		$rentals = $this->getAllRentals ();
		
		return $this->render ( "editingMovie.html.twig", array (
				"user" => $user,
				"movie" => $movie,
				"movies" => $movies,
				"genres" => $genres,
				"rentals" => $rentals 
		) );
	}
	
	/**
	 * @Route("/updateMovie", name="updateMovie")
	 */
	public function updateMovieAction(Request $request) {
		/* $logger = $this->container->get("logger"); */
		$em = $this->getDoctrine ()->getManager ();
		$user = $request->getSession ()->get ( "user" );
		$movieId = $request->request->get ( "updateMovieId" );
		
		$movie = $this->getTheMovie ( $movieId );
		
		$movie->setMovieId ( $movieId );
		$movie->setMovieName ( $request->request->get ( "updateMovieName" ) );
		$movie->setGenreId ( $request->request->get ( "updateMovieGenre" ) );
		$movie->setMovieDesc ( $request->request->get ( "updateMovieDesc" ) );
		$em->flush ();
		$this->addFlash ( "notice", "The movie " . $movie->getMovieName () . " has been updated" );
		
		$movies = $this->getAllMovies ();
		$genres = $this->getAllGenres ();
		$rentals = $this->getAllRentals ();
		return $this->render ( "manageMovies.html.twig", array (
				"user" => $user,
				"movies" => $movies,
				"genres" => $genres,
				"rentals" => $rentals 
		) );
	}
	
	/**
	 * @Route("/removeMovie/{movieId}", name="removeMovie", requirements={"page": "\d+"})
	 */
	public function removeMovieAction(Request $request, $movieId) {
		$user = $request->getSession ()->get ( "user" );
		
		$em = $this->getDoctrine ()->getManager ();
		$movie = $this->getTheMovie ( $movieId );
		$em->remove ( $movie );
		$em->flush ();
		$this->addFlash ( "notice", "The movie " . $movie->getMovieName () . " has been removed" );
		
		$movies = $this->getAllMovies ();
		$genres = $this->getAllGenres ();
		$rentals = $this->getAllRentals ();
		return $this->render ( "manageMovies.html.twig", array (
				"user" => $user,
				"movies" => $movies,
				"genres" => $genres,
				"rentals" => $rentals 
		) );
	}
	
	/**
	 * @Route("/search", name="search")
	 */
	public function searchAction(Request $request) {
		$user = $request->getSession ()->get ( "user" );
		$movies = $this->getAllMovies ();
		$rentals = $this->getAllRentals ();
		if ($user) {
			return $this->render ( "search.html.twig", array (
					"movies" => $movies,
					"rentals" => $rentals 
			) );
		}
	}
	
	/**
	 * @Route("/searchFor", name="searchFor")
	 */
	public function searchForAction(Request $request) {
		$user = $request->getSession ()->get ( "user" );
		$searchString = $request->request->get ( "searchString" );
		
		$em = $this->getDoctrine ()->getManager ();
		$query = $em->createQuery ( 'SELECT m
    FROM AppBundle:Movie m
    WHERE m.movie_name LIKE :searchString' )->setParameter ( 'searchString', $searchString );
		
		$moviesFound = $query->getResult ();
		
		$genres = $this->getAllGenres();
		$movies = $this->getAllMovies ();
		$rentals = $this->getAllRentals ();
		if ($user) {
			return $this->render ( "searchFor.html.twig", array (
					"user" => $user,
					"movies" => $movies,
					"rentals" => $rentals,
					"genres"=> $genres,
					"moviesFound" => $moviesFound,
			) );
		}else{
			$this->addFlash("error", "You are not allowed to be here!");
			return $this->render("login.html.twig");
		}
		
	}
	
	/**
	 * @Route("/searchFor2", name="searchFor2")
	 */
	public function searchFor2Action(Request $request) {
		$logger = $this->container->get("logger");
		$user = $request->getSession()->get("user");
		$movies = $this->getAllMovies();
		$searchString = $request->request->get("searchString");
		$moviesFound = array();
		$iterator = 0;
		for($i=0; $i<count($movies);$i++){
			$movieName = $movies[$i]->getMovieName();
			$parts = explode(' ', $movieName);
			for($j=0;$j<count($parts);$j++){
				$logger->error("HOLAAA  ".$parts[$j]."  ".$searchString);
				if(strcmp($parts[$j], $searchString)== 0 /*$parts[$j] === $searchString*/){
					$logger->error("coincidence!!!!!!!!");
					$moviesFound[$iterator] = $movies[$i];
					$iterator++;
				}
			}
		}
		$genres = $this->getAllGenres();
		$rentals = $this->getAllRentals ();
		if ($user) {
			return $this->render ( "searchFor.html.twig", array (
					"user" => $user,
					"movies" => $movies,
					"rentals" => $rentals,
					"genres"=> $genres,
					"moviesFound" => $moviesFound,
			) );
		}else{
			$this->addFlash("error", "You are not allowed to be here!");
			return $this->render("login.html.twig");
		}
	
	}
	
	/* Fetching all the movies */
	public function getAllMovies() {
		$moviesRepository = $this->getDoctrine ()->getRepository ( "AppBundle:Movie" );
		$movies = $moviesRepository->findAll ();
		if ($movies) {
			return $movies;
		} else {
			return null;
		}
	}
	public function getTheMovie($movieId) {
		$moviesRepository = $this->getDoctrine ()->getRepository ( "AppBundle:Movie" );
		$movie = $moviesRepository->find ( $movieId );
		if ($movie) {
			return $movie;
		} else {
			return null;
		}
	}
	
	/* Fetching all the genres */
	public function getAllGenres() {
		$genresRepository = $this->getDoctrine ()->getRepository ( "AppBundle:Genre" );
		$genres = $genresRepository->findAll ();
		if ($genres) {
			return $genres;
		} else {
			return null;
		}
	}
	
	/* Fetching all rentals */
	public function getAllRentals() {
		$rentalsRepository = $this->getDoctrine ()->getRepository ( "AppBundle:Rental" );
		$rentals = $rentalsRepository->findAll ();
		if ($rentals) {
			return $rentals;
		} else {
			return null;
		}
	}
	
	/* Register any object to DB */
	public function registerToDB($object) {
		try {
			$em = $this->getDoctrine ()->getManager ();
			$em->persist ( $object );
			$em->flush ();
		} catch ( Exeption $e ) {
			return false;
		}
		return true;
	}
}