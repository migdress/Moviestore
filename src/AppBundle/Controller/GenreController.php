<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Utils\Constants;
use AppBundle\Entity\Genre;

class GenreController extends Controller {

    /**
     * 
     * @Route("/manageGenres", name="manageGenres")
     */
    public function manageGenresAction(Request $request) {
        $user = $this->validateSession();
        if ($user != null && $user->hasTheRole(Constants::USER_TYPE_ADMIN)) {
            $genres = $this->getAllGenresFromDB();
            return $this->render(Constants::VIEW_MANAGE_GENRES, array(
                        "genres" => $genres,
                        "constants" => Constants::get()
            ));
        } else {
            $this->addFlash("error", "You are not authenticated, please login");
            return $this->redirectToRoute("logout");
        }
    }

    /**
     * @Route("/registerGenre", name="registerGenre")
     */
    public function registerGenreAction(Request $request) {
        $user = $this->validateSession();
        if ($user != null && $user->hasTheRole(Constants::USER_TYPE_ADMIN)) {
            $resultFlag = $this->saveGenreToDB($request->request->get("registerGenreName"));
            if ($resultFlag == 1) {
                $this->addFlash(Constants::FLASH_NOTICE, Constants::MSG_GENRE_REG_PREF . $request->request->get("registerUserName") . Constants::MSG_GENRE_REG_SUFF);
            } else {
                $this->addFlash(Constants::FLASH_ERROR, Constants::MSG_BAD_GENRE_REG);
            }
            return $this->render(Constants::VIEW_MANAGE_GENRES, [
                        "genres" => $this->getAllGenresFromDB(),
                        "constants" => Constants::get()
            ]);
        } else {
            $this->addFlash("error", "You are not authenticated, please login");
            return $this->redirectToRoute("logout");
        }
    }

    /**
     * @Route("/editGenre/{genreId}", name="editGenre", requirements={"genreId": "\d+"})
     */
    public function editGenreAction(Request $request, $genreId) {
        $user = $this->validateSession();
        if ($user != null && $user->hasTheRole(Constants::USER_TYPE_ADMIN)) {
            $genreEdit = $this->getTheGenreFromDB($genreId);
            return $this->render(Constants::VIEW_EDIT_GENRE, ["constants" => Constants::get(),
                        "genre" => $genreEdit]);
        } else {
            $this->addFlash("error", "You are not authenticated, please login");
            return $this->redirectToRoute("logout");
        }
    }

    /**
     * @Route("/updateGenre", name="updateGenre")
     */
    public function updateGenreAction(Request $request) {
        $user = $this->validateSession();
        if ($user != null && $user->hasTheRole(Constants::USER_TYPE_ADMIN)) {
            $resultFlag = $this->updateGenreToDB($request->request->get("updateGenreId"), $request->request->get("updateGenreName"));
            if ($resultFlag == 1) {
                $this->addFlash(Constants::FLASH_NOTICE, Constants::MSG_GENRE_EDIT_PREF . $request->request->get("updateGenreName") . Constants::MSG_GENRE_EDIT_SUFF);
            } else {
                $this->addFlash(Constants::FLASH_ERROR, Constants::MSG_BAD_GENRE_EDIT);
            }
            return $this->render(Constants::VIEW_MANAGE_GENRES, [
                        "constants" => Constants::get(),
                        "genres" => $this->getAllGenresFromDB()
            ]);
        } else {
            $this->addFlash("error", "You are not authenticated, please login");
            return $this->redirectToRoute("logout");
        }
    }

    /**
     * @Route("/removeGenre/{genreId}", name="removeGenre", requirements={"genreId": "\d+"})
     */
    public function removeGenreAction(Request $request, $genreId) {
        $user = $this->validateSession();
        if ($user != null && $user->hasTheRole(Constants::USER_TYPE_ADMIN)) {
            $genreRemoveName = $this->getTheGenreFromDB($genreId)->getGenreName();
            $resultFlag = $this->removeGenreFromDB($genreId);
            if ($resultFlag == 1) {
                $this->addFlash(Constants::FLASH_NOTICE, Constants::MSG_GENRE_REM_PREF . $genreRemoveName . Constants::MSG_GENRE_REM_SUFF);
            } else {
                $this->addFlash(Constants::FLASH_ERROR, Constants::MSG_BAD_GENRE_REM);
            }
            return $this->render(Constants::VIEW_MANAGE_GENRES, [
                        "genres" => $this->getAllGenresFromDB(),
                        "constants" => Constants::get()
            ]);
        } else {
            $this->addFlash("error", "You are not authenticated, please login");
            return $this->redirectToRoute("logout");
        }
    }
    
    public function validateSession() {
        /* This line allows to check if the user is authenticated */
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            /* The line below should return the user object if authenticated */
            #$userSession = $this->get('security.token_storage')->getToken()->getUser();
            $this->get("logger")->info("User is authenticated fully");
            $userSession = $this->getUser(); #this line is a shortcut for the one commented above
            if ($userSession != null) {
                //$userSession->loadRoles($this->getDoctrine()->getManager());
                $this->get("logger")->info("User session is not null");
                $this->get("logger")->info("Roles: ".implode(",",$userSession->getRoles()));
                return $userSession;
            } else {
                return null;
            }
        } else {
            throw $this->createAccessDeniedException();
        }
    }
    
    private function getAllGenresFromDB(){
        $genresRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:Genre");
        return $genresRepository->getAllGenres();
    }
    
    private function getTheGenreFromDB($genreId){
        $genresRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:Genre");
        $genre = $genresRepository->find($genreId);
        if($genre){
            return $genre;
        }else{
            return null;
        }
    }
    
    private function saveGenreToDB($genre_name){
        $genre = new Genre();
        $genre->setGenreName($genre_name);
        $this->getDoctrine()->getManager()->persist($genre);
        $this->getDoctrine()->getManager()->flush();
        return 1;
    }
    
    private function updateGenreToDB($genre_id, $genre_name){
        $genre = $this->getTheGenreFromDB($genre_id);
        $genre->setGenreName($genre_name);
        $this->getDoctrine()->getManager()->flush();
        return 1;
    }
    
    private function removeGenreFromDB($genre_id){
        $genre = $this->getTheGenreFromDB($genre_id);
        $this->removeAllMoviesWithGenre($genre_id);
        $this->getDoctrine()->getManager()->remove($genre);
        $this->getDoctrine()->getManager()->flush();
        return 1;
    }
    
    private function removeAllMoviesWithGenre($genreId){
        $movie_has_genreRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:Movie_has_Genre");
        $records = $movie_has_genreRepository->findBy(["genre" => $genreId]);
        foreach ($records as $record) {
            $this->getDoctrine()->getManager()->remove($record);
        }
        $this->getDoctrine()->getManager()->flush();
        return 1;
    }
    
    
}
