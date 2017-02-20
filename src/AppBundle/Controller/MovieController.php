<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Utils\Constants;
use AppBundle\Entity\User;
use AppBundle\Entity\Movie;
use AppBundle\Entity\Rental;
use AppBundle\Entity\Genre;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class MovieController extends Controller {

    /**
     * @Route("/manageMovies", name="manageMovies")
     */
    public function manageMoviesAction(Request $request) {
        $user = $request->getSession()->get("user");
        if ($user != null) {
            if ($user->getUserType() == "ADMIN") {
                return $this->render("manageMovies.html.twig", [
                            "user" => $user,
                            "movies" => Movie::getAllMovies($this->getDoctrine()->getManager()),
                            "genres" => Genre::getAllGenres($this->getDoctrine()->getManager()),
                            "constants" => Constants::get()
                ]);
            } else {
                $this->addFlash("error", "You are not allowed to be here!");
                return $this->render("accout.html.twig", ["constants" => Constants::get()]);
            }
        } else {
            $this->addFlash("error", "Please log in to you account");
            return $this->render("login.html.twig", ["constants" => Constants::get()]);
        }
    }

    /**
     * @Route("/registerMovie", name="registerMovie")
     */
    public function registerMovieAction(Request $request) {
        $user = $request->getSession()->get("user");
        if ($user != null) {
            $imageFile = $request->files->get("registerMovieImage");
            $imageName = null;
            if($imageFile != null){
                $imageName =$request->request->get("registerMovieName").".".$imageFile->guessExtension();
            }
            $resultFlag = Movie::register($request->request->get("registerMovieGenres"), $request->request->get("registerMovieName"), $request->request->get("registerMoviePrice"), $imageName, $request->request->get("registerMovieDesc"), $this->getDoctrine()->getManager());
            if ($resultFlag > 0 ){
                if($imageFile != null){
                $imageFile->move(Constants::DIR_MOVIE_IMAGES, $imageName);
                }
                $this->addFlash("notice", "The movie " . $request->request->get("registerMovieName") . " has been registered succesfuly");
            }
            else {
                $this->addFlash("error", "An error ocurred, movie not registered");
            }
            return $this->render("manageMovies.html.twig", array(
                        "user" => $user,
                        "movies" => Movie::getAllMovies($this->getDoctrine()->getManager()),
                        "genres" => Genre::getAllGenres($this->getDoctrine()->getManager()),
                        "constants" => Constants::get()
            ));
        } else {
            $this->addFlash("error", "Please login to your account");
            return $this->render("accout.html.twig", ["constants" => Constants::get()]);
        }
    }

    /**
     * @Route("/editMovie/{movieId}", name="editMovie", requirements={"movieId": "\d+"})
     */
    public function editMovieAction(Request $request, $movieId) {
        $user = $request->getSession()->get("user");
        if ($user != null) {
            $movie = Movie::getTheMovie($movieId, $this->getDoctrine()->getManager());
            return $this->render(Constants::VIEW_EDIT_MOVIE, [
                        "user" => $user,
                        "movie" => $movie,
                        "genres" => Genre::getAllGenres($this->getDoctrine()->getManager()),
                        "constants" => Constants::get()
            ]);
        } else {
            $this->addFlash("error", "please login to your account");
            $this->render("login.html.twig", ["constants" => Constants::get()]);
        }
    }

    /**
     * @Route("/updateMovie", name="updateMovie")
     */
    public function updateMovieAction(Request $request) {
        $user = $request->getSession()->get("user");
        if ($user != null) {
            $imageFile = $request->files->get("updateMovieImage");
            $imageName = null;
            if($imageFile != null){
                $imageName =$request->request->get("updateMovieName").".".$imageFile->guessExtension();
            }
            $resultFlag = Movie::update($request->request->get("updateMovieId"), $request->request->get("updateMovieGenres"), $request->request->get("updateMovieName"), $request->request->get("updateMoviePrice"), $imageName, $request->request->get("updateMovieDesc"), $this->getDoctrine()->getManager());
            if ($resultFlag > 0) {
                if ($imageFile != null) {
                    $fs = $this->get("filesystem");
                    if ($fs->exists(Constants::DIR_MOVIE_IMAGES."/".$imageName)) {
                        $fs->remove(Constants::DIR_MOVIE_IMAGES."/".$imageName);
                    }
                    $imageFile->move(Constants::DIR_MOVIE_IMAGES, $imageName);
                }
                $this->addFlash("notice", "The movie " . $request->request->get("updateMovieName") . " has been updated");
            } else {
                $this->addFlash("error", "An error ocurred, the movie was not updated");
            }
            return $this->render("manageMovies.html.twig", [
                        "user" => $user,
                        "movies" => Movie::getAllMovies($this->getDoctrine()->getManager()),
                        "genres" => Genre::getAllGenres($this->getDoctrine()->getManager()),
                        "constants" => Constants::get()
            ]);
        } else {
            $this->addFlash("error", "Please login to your account");
            $this->render("login.html.twig", ["constants" => Constants::get()]);
        }
    }

    /**
     * @Route("/removeMovie/{movieId}", name="removeMovie", requirements={"page": "\d+"})
     */
    public function removeMovieAction(Request $request, $movieId) {
        $user = $request->getSession()->get("user");
        if ($user != null) {
            $movieName = Movie::getTheMovie($movieId, $this->getDoctrine()->getManager())->getMovieName();
            $imageName = Movie::getTheMovie($movieId, $this->getDoctrine()->getManager())->getMovieImagePath();
            $resultFlag = Movie::remove($movieId, $this->getDoctrine()->getManager());
            if ($resultFlag == 1) {
                if ($imageName != null) {
                    $fs = $this->get("filesystem");
                    $fs->remove(Constants::DIR_MOVIE_IMAGES . "/" . $imageName);
                }
                $this->addFlash(Constants::FLASH_NOTICE, "The movie " . $movieName . " has been removed");
            } else {
                $this->addFlash(Constants::FLASH_ERROR, "An error ocurred, the movie was not removed");
            }
            return $this->render("manageMovies.html.twig", array(
                        "user" => $user,
                        "movies" => Movie::getAllMovies($this->getDoctrine()->getManager()),
                        "genres" => Genre::getAllGenres($this->getDoctrine()->getManager()),
                        "constants" => Constants::get()
            ));
        } else {
            $this->addFlash("error", "Please login to your account");
            $this->render("login.html.twig", ["constants" => Constants::get()]);
        }
    }

    /**
     * @Route("/search", name="search")
     */
    public function searchAction(Request $request) {
        $user = $request->getSession()->get("user");
        $movies = $this->getAllMovies();
        $rentals = $this->getAllRentals();
        if ($user) {
            return $this->render("search.html.twig", array(
                        "movies" => $movies,
                        "rentals" => $rentals
            ));
        }
    }

    /**
     * @Route("/searchFor", name="searchFor")
     */
    public function searchForAction(Request $request) {
        $user = $request->getSession()->get("user");
        $searchString = $request->request->get("searchString");

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT m
    FROM AppBundle:Movie m
    WHERE m.movie_name LIKE :searchString')->setParameter('searchString', $searchString);

        $moviesFound = $query->getResult();

        $genres = $this->getAllGenres();
        $movies = $this->getAllMovies();
        $rentals = $this->getAllRentals();
        if ($user) {
            return $this->render("searchFor.html.twig", array(
                        "user" => $user,
                        "movies" => $movies,
                        "rentals" => $rentals,
                        "genres" => $genres,
                        "moviesFound" => $moviesFound,
            ));
        } else {
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
        for ($i = 0; $i < count($movies); $i++) {
            $movieName = $movies[$i]->getMovieName();
            $parts = explode(' ', $movieName);
            for ($j = 0; $j < count($parts); $j++) {
                $logger->error("HOLAAA  " . $parts[$j] . "  " . $searchString);
                if (strcmp($parts[$j], $searchString) == 0 /* $parts[$j] === $searchString */) {
                    $logger->error("coincidence!!!!!!!!");
                    $moviesFound[$iterator] = $movies[$i];
                    $iterator++;
                }
            }
        }
        $genres = $this->getAllGenres();
        $rentals = $this->getAllRentals();
        if ($user) {
            return $this->render("searchFor.html.twig", array(
                        "user" => $user,
                        "movies" => $movies,
                        "rentals" => $rentals,
                        "genres" => $genres,
                        "moviesFound" => $moviesFound,
            ));
        } else {
            $this->addFlash("error", "You are not allowed to be here!");
            return $this->render("login.html.twig");
        }
    }

}
