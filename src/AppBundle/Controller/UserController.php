<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Entity\Movie;
use AppBundle\Entity\Rental;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class UserController extends Controller {

	/**
	 * @Route("/manageUsers", name="manageUsers")
	 */
	public function manageUsersAction(Request $request) {
		$user = $request->getSession ()->get ( "user" );
		if ($user->getUserType () == "ADMIN") {
			$users = $this->getAllUsers();
			$rentals = $this->getAllRentals();
			return $this->render ( "manageUsers.html.twig", array (
					"user" => $user,
					"users" => $users,
					"rentals" => $rentals,
			) );
		} else {
			$this->addFlash ( "error", "You are not allowed to be here!" );
			return $this->render ( "accout.html.twig" );
		}
	}
	
	/**
	 * @Route("/registerUser", name="registerUser")
	 */
	public function registerMovieAction(Request $request){
		$user = $request->getSession()->get("user");
		
		$newUser = new User();
		
		$newUser->setUserId($request->request->get("registerUserId"));
		$newUser->setUserName($request->request->get("registerUserName"));
		$newUser->setUserLastName($request->request->get("registerUserLastame"));
		$newUser->setUserLogin($request->request->get("registerUserLogin"));
		
		/* Hashing the password coming from the user input */
		$plainPassword = $request->request->get ( "registerUserPassword" );
		$hashedPassword = sha1 ( $plainPassword );
		$newUser->setUserPassword ( $hashedPassword );
		
		$newUser->setUserType($request->request->get("registerUserType"));
		
		$logger = $this->container->get("logger");
		$logger->error("AQUIIIIII".$user->getUserLastName());
		
		$this->registerToDB($newUser);
		$this->addFlash("notice", "The user ".$newUser->getUserName()." has been registered succesfuly");
	
		$users = $this->getAllUsers();
		$rentals = $this->getAllRentals();
		return $this->render ( "manageUsers.html.twig", array (
				"user" => $user,
				"users" => $users,
				"rentals" => $rentals,
		) );
	}
	
	/**
	 * @Route("/editUser/{userId}", name="editUser", requirements={"userId": "\d+"})
	 */
	public function editMovieAction(Request $request, $userId){

	}
	
	/**
	 * @Route("/removeUser/{userId}", name="removeUser", requirements={"userId": "\d+"})
	 */
	public function removeMovieAction(Request $request, $userId){

	}
	
	/**
	 * @Route("/updateUser", name="updateUser")
	 */
	public function updateUserAction(Request $request){
		
	}
	
	/**
	 * @Route("/userRentals", name="userRentals")
	 */
	public function searchAction(Request $request){
		$user = $request->getSession()->get("user");
		$rentals = $this->getAllRentals();
		$movies = $this->getAllMovies();
		$userRentals= array();
		$iterator = 0;
		for($i=0;$i<count($rentals);$i++){
			if($user->getUserId() == $rentals[$i]->getUserId()){
				$userRentals[$iterator] = $rentals[$i];
			}
		}
		
		
		return $this->render ( "userRentals.html.twig", array (
				"user" => $user,
				"movies" => $movies,
				"rentals" => $rentals,
				"userRentals" => $userRentals,
		) );
	}
	
	/**
	 * @Route("/rentMovie/{userId}{movieId}", name="rentMovie")
	 */
	public function rentMovieAction(Request $request, $userId, $movieId){
		$logger = $this->container->get("logger");
		$user = $request->getSession()->get("user");
		$rented = false;
		
		$rentals = $this->getAllRentals();
		for($i=0; $i<count($rentals);$i++){
			if($movieId == $rentals[$i]->getMovieId() && $rentals[$i]->getRentalStatus() == "VALID"){
				$rented = true;
				$users = $this->getAllUsers();
				$rentals = $this->getAllRentals();
				$this->addFlash("error", "The movie is already rented");
				return $this->render ( "search.html.twig", array (
						"user" => $user,
						"users" => $users,
						"rentals" => $rentals,
				) );
			}
		}
		if(!$rented){
			$rental = new Rental();
			$rental->setMovieId($movieId);
			$rental->setUserId($userId);
			$rental->setRentalInitDate(date("d/m/y"));
			$rental->setRentalEndDate(strtotime("+1 day", strtotime("today")));
			$rental->setRentalStatus("VALID");
			$this->registerToDB($rental);
			$this->addFlash("notice", "The movie has been rented");
			$users = $this->getAllUsers();
			$rentals = $this->getAllRentals();
			return $this->render ( "search.html.twig", array (
					"user" => $user,
					"users" => $users,
					"rentals" => $rentals,
			) );
		}
	}
	
	/**
	 * @Route("/retrieveMovie/{userId}{movieId}", name="rentMovie")
	 */
	public function retrieveMovieAction(Request $request, $userId, $movieId){
		$logger = $this->container->get("logger");
		$user = $request->getSession()->get("user");
		$rented = false;
	
		$rentals = $this->getAllRentals();
		for($i=0; $i<count($rentals);$i++){
			if($movieId == $rentals[$i]->getMovieId() && $rentals[$i]->getRentalStatus() == "VALID"){
				$rented = true;
				$users = $this->getAllUsers();
				$rentals = $this->getAllRentals();
				$this->addFlash("error", "The movie is already rented");
				return $this->render ( "search.html.twig", array (
						"user" => $user,
						"users" => $users,
						"rentals" => $rentals,
				) );
			}
		}
		if(!$rented){
			$rental = new Rental();
			$rental->setMovieId($movieId);
			$rental->setUserId($userId);
			$rental->setRentalInitDate(date("d/m/y"));
			$rental->setRentalEndDate(strtotime("+1 day", strtotime("today")));
			$rental->setRentalStatus("VALID");
			$this->registerToDB($rental);
			$this->addFlash("notice", "The movie has been rented");
			$users = $this->getAllUsers();
			$rentals = $this->getAllRentals();
			return $this->render ( "search.html.twig", array (
					"user" => $user,
					"users" => $users,
					"rentals" => $rentals,
			) );
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
	
	
	/* Fetching all the users */
	public function getAllUsers() {
		$usersRepository = $this->getDoctrine ()->getRepository ( "AppBundle:User" );
		$users = $usersRepository->findAll ();
		if ($users) {
			return $users;
		} else {
			return null;
		}
	}
	
	/*Fetching all rentals*/
	public function getAllRentals() {
		$rentalsRepository = $this->getDoctrine ()->getRepository ( "AppBundle:Rental" );
		$rentals = $rentalsRepository->findAll ();
		if ($rentals) {
			return $rentals;
		} else {
			return null;
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
}