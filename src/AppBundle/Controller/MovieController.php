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
        //$user = $this->container->get("securityController")->validateSession();
        $user = $this->getUser();
        if ($user != null && $user->hasTheRole(Constants::USER_TYPE_ADMIN)) {
            return $this->render("manageMovies.html.twig", [
                        "movies" => $this->getAllMoviesFromDB(),
                         "genres" => $this->getAllGenresFromDB(),
                        "constants" => Constants::get()
            ]);
        } else {
            $this->addFlash("error", "You are not authenticated, please login");
            return $this->redirectToRoute("logout");
        }
    }

    /**
     * @Route("/registerMovie", name="registerMovie")
     */
    public function registerMovieAction(Request $request) {
        $user = $this->getUser();
        if ($user != null && $user->hasTheRole(Constants::USER_TYPE_ADMIN)) {
            $imageFile = $request->files->get("registerMovieImage");
            $imageName = null;
            if ($imageFile != null) {
                $imageName = $request->request->get("registerMovieName") . "." . $imageFile->guessExtension();
            }
            $resultFlag = $this->saveMovieToDB($request->request->get("registerMovieGenres"), $request->request->get("registerMovieName"), $request->request->get("registerMoviePrice"), $imageName, $request->request->get("registerMovieDesc"));
            if ($resultFlag > 0) {
                if ($imageFile != null) {
                    $imageFile->move(Constants::DIR_MOVIE_IMAGES, $imageName);
                }
                $this->addFlash("notice", "The movie " . $request->request->get("registerMovieName") . " has been registered succesfuly");
            } else {
                $this->addFlash("error", "An error ocurred, movie not registered");
            }
            return $this->render("manageMovies.html.twig", array(
                        "movies" => $this->getAllMoviesFromDB(),
                        "genres" => $this->getAllGenresFromDB(),
                        "constants" => Constants::get()
            ));
        } else {
            $this->addFlash("error", "You are not authenticated, please login");
            return $this->redirectToRoute("logout");
        }
    }

    /**
     * @Route("/editMovie/{movieId}", name="editMovie", requirements={"movieId": "\d+"})
     */
    public function editMovieAction(Request $request, $movieId) {
        $user = $this->getUser();
        if ($user != null && $user->hasTheRole(Constants::USER_TYPE_ADMIN)) {
            $movie = $this->getTheMovieFromDB($movieId);
            return $this->render(Constants::VIEW_EDIT_MOVIE, [
                        "movie" => $movie,
                        "genres" => $this->getAllGenresFromDB(),
                        "constants" => Constants::get()
            ]);
        } else {
            $this->addFlash("error", "You are not authenticated, please login");
            return $this->redirectToRoute("logout");
        }
    }

    /**
     * @Route("/updateMovie", name="updateMovie")
     */
    public function updateMovieAction(Request $request) {
        $user = $this->getUser();
        if ($user != null && $user->hasTheRole(Constants::USER_TYPE_ADMIN)) {
            $imageFile = $request->files->get("updateMovieImage");
            $imageName = null;
            if ($imageFile != null) {
                $imageName = $request->request->get("updateMovieName") . "." . $imageFile->guessExtension();
            }
            $resultFlag = $this->UpdateMovieToDB($request->request->get("updateMovieId"), $request->request->get("updateMovieGenres"), $request->request->get("updateMovieName"), $request->request->get("updateMoviePrice"), $imageName, $request->request->get("updateMovieDesc"));
            if ($resultFlag > 0) {
                if ($imageFile != null) {
                    $fs = $this->get("filesystem");
                    if ($fs->exists(Constants::DIR_MOVIE_IMAGES . "/" . $imageName)) {
                        $fs->remove(Constants::DIR_MOVIE_IMAGES . "/" . $imageName);
                    }
                    $imageFile->move(Constants::DIR_MOVIE_IMAGES, $imageName);
                }
                $this->addFlash("notice", "The movie " . $request->request->get("updateMovieName") . " has been updated");
            } else {
                $this->addFlash("error", "An error ocurred, the movie was not updated");
            }
            return $this->render("manageMovies.html.twig", [
                        "user" => $user,
                        "movies" => $this->getAllMoviesFromDB(),
                        "genres" => $this->getAllGenresFromDB(),
                        "constants" => Constants::get()
            ]);
        } else {
            $this->addFlash("error", "You are not authenticated, please login");
            return $this->redirectToRoute("logout");
        }
    }

    /**
     * @Route("/removeMovie/{movieId}", name="removeMovie", requirements={"page": "\d+"})
     */
    public function removeMovieAction(Request $request, $movieId) {
        $user = $this->getUser();
        if ($user != null && $user->hasTheRole(Constants::USER_TYPE_ADMIN)) {
            $movieName = $this->getTheMovieFromDB($movieId)->getMovieName();
            $imageName = $this->getTheMovieFromDB($movieId)->getMovieImagePath();
            $resultFlag = $this->removeTheMovieFromDB($movieId);
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
                        "movies" => $this->getAllMoviesFromDB(),
                        "genres" => $this->getAllGenresFromDB(),
                        "constants" => Constants::get()
            ));
        } else {
            $this->addFlash("error", "You are not authenticated, please login");
            return $this->redirectToRoute("logout");
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


    private function saveMovieToDB(Array $movie_genres, $movie_name, $movie_price, $imageName, $movie_desc) {
        $genreRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:Genre");
        $movie = new Movie();
        $movie->setMovieName($movie_name);
        $movie->setMoviePrice($movie_price);
        $movie->setMovieDesc($movie_desc);
        $this->getDoctrine()->getManager()->persist($movie);
        foreach ($movie_genres as $movie_genre) {
            $movie_has_genre = new Movie_has_Genre();
            $movie_has_genre->setGenre($genreRepository->find($movie_genre));
            $movie_has_genre->setMovie($movie);
            $movie->addMovieHasGenre($movie_has_genre);
            $em->flush();
        }
        if ($imageName != null) {
            $movie->setMovieImagePath($imageName);
        }
        $em->flush();
        return $movie->getMovieId();
    }

    private function getTheMovieFromDB($movieId) {
        $movieRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:Movie");
        return $movieRepository->find($movieId);
    }

    private function updateMovieToDB($movie_id, Array $movie_genres = null, $movie_name, $movie_price, $imageName, $movie_desc) {
        $movie = $this->getTheMovieFromDB($movie_id);
        $movie->setMovieName($movie_name);
        $movie->setMoviePrice($movie_price);
        $movie->setMovieDesc($movie_desc);
        if ($imageName != null) {
            $movie->setMovieImagePath($imageName);
        }
        if ($movie_genres) {
            $this->removeAllMovieGenres($movie_id);
            foreach ($movie_genres as $movie_genre) {
                $movie_has_genre = new Movie_has_Genre();
                $movie_has_genre->setGenre(Genre::getTheGenre($movie_genre, $em));
                $movie_has_genre->setMovie($movie);
                $movie->addMovieHasGenre($movie_has_genre);
                $this->getDoctrine()->getManager()->flush();
            }
        }
        $em->flush();
        return $movie->getMovieId();
    }

    private function removeTheMovieFromDB($movieId) {
        $movie = Movie::getTheMovie($movie_id, $em);
        $this->removeAllMovieGenres($movieId);
        $em->remove($movie);
        $em->flush();
        return 1;
    }

    private function removeAllMovieGenres($movieId) {
        $movie_has_genreRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:Movie_has_Genre");
        $records = $movie_has_genreRepository->findBy(["movie" => $movieId]);
        foreach ($records as $record) {
            $this->getDoctrine()->getManager()->remove($record);
        }
        $this->getDoctrine()->getManager()->flush();
        return 1;
    }

    private function getAllMoviesFromDB() {
        $movieRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:Movie");
        $movies = $movieRepository->getAllMovies();
        return $movies;
    }

    private function getAllGenresFromDB() {
        $genreRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:Genre");
        $genres = $genreRepository->getAllGenres();
        return $genres;
    }

}
