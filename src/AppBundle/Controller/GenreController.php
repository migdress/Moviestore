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
        $user = $request->getSession()->get("user");
        if ($user != null) {
            if ($user->getUserType() == "ADMIN") {
                $genres = Genre::getAllGenres($this->getDoctrine()->getManager());
                return $this->render(Constants::VIEW_MANAGE_GENRES, array(
                            "user" => $user,
                            "genres" => $genres,
                            "constants" => Constants::get()
                ));
            } else {
                $this->addFlash(Constants::FLASH_ERROR, Constants::MSG_ACCESS_DENIED);
            }
        } else {
            $this->addFlash(Constants::FLASH_ERROR, Constants::MSG_LOGIN);
        }
        return $this->render(Constants::VIEW_LOGIN, ["constants" => Constants::get()]);
    }

    /**
     * @Route("/registerGenre", name="registerGenre")
     */
    public function registerGenreAction(Request $request) {
        $user = $request->getSession()->get("user");
        if ($user != null) {
            $resultFlag = Genre::register($request->request->get("registerGenreName"), $this->getDoctrine()->getManager());
            if ($resultFlag == 1) {
                $this->addFlash(Constants::FLASH_NOTICE, Constants::MSG_GENRE_REG_PREF . $request->request->get("registerUserName") . Constants::MSG_GENRE_REG_SUFF);
            } else {
                $this->addFlash(Constants::FLASH_ERROR, Constants::MSG_BAD_GENRE_REG);
            }
            return $this->render(Constants::VIEW_MANAGE_GENRES, [
                        "user" => $user,
                        "genres" => Genre::getAllGenres($this->getDoctrine()->getManager()),
                        "constants" => Constants::get()
            ]);
        } else {
            $this->addFlash(Constants::FLASH_ERROR, Constants::MSG_LOGIN);
            return $this->render(Constants::VIEW_LOGIN, ["constants" => Constants::get()]);
        }
    }

    /**
     * @Route("/editGenre/{genreId}", name="editGenre", requirements={"genreId": "\d+"})
     */
    public function editGenreAction(Request $request, $genreId) {
        $user = $request->getSession()->get("user");
        if ($user != null) {
            $genreEdit = Genre::getTheGenre($genreId, $this->getDoctrine()->getManager());
            return $this->render(Constants::VIEW_EDIT_GENRE, ["constants" => Constants::get(),
                        "genre" => $genreEdit]);
        } else {
            $this->addFlash(Constants::FLASH_ERROR, Contants::MSG_LOGIN);
            return $this->render(Constants::VIEW_LOGIN, ["constants" => Constants::get()]);
        }
    }

    /**
     * @Route("/updateGenre", name="updateGenre")
     */
    public function updateGenreAction(Request $request) {
        $user = $request->getSession()->get("user");
        if ($user != null) {
            $resultFlag = Genre::update($request->request->get("updateGenreId"), $request->request->get("updateGenreName"), $this->getDoctrine()->getManager());
            if ($resultFlag == 1) {
                $this->addFlash(Constants::FLASH_NOTICE, Constants::MSG_GENRE_EDIT_PREF . $request->request->get("updateGenreName") . Constants::MSG_GENRE_EDIT_SUFF);
            } else {
                $this->addFlash(Constants::FLASH_ERROR, Constants::MSG_BAD_GENRE_EDIT);
            }
            return $this->render(Constants::VIEW_MANAGE_GENRES, ["constants" => Constants::get(), "genres" => Genre::getAllGenres($this->getDoctrine()->getManager())]);
        } else {
            $this->addFlash(Constants::FLASH_ERROR, Contants::MSG_LOGIN);
            return $this->render(Constants::VIEW_LOGIN, ["constants" => Constants::get()]);
        }
    }

    /**
     * @Route("/removeGenre/{genreId}", name="removeGenre", requirements={"genreId": "\d+"})
     */
    public function removeGenreAction(Request $request, $genreId) {
        $user = $request->getSession()->get("user");
        if ($user != null) {
            $genreRemoveName = Genre::getTheGenre($genreId, $this->getDoctrine()->getManager())->getGenreName();
            $resultFlag = Genre::remove($genreId, $this->getDoctrine()->getManager());
            if ($resultFlag == 1) {
                $this->addFlash(Constants::FLASH_NOTICE, Constants::MSG_GENRE_REM_PREF . $genreRemoveName . Constants::MSG_GENRE_REM_SUFF);
            } else {
                $this->addFlash(Constants::FLASH_ERROR, Constants::MSG_BAD_GENRE_REM);
            }
            return $this->render(Constants::VIEW_MANAGE_GENRES, [
                        "genres" => Genre::getAllGenres($this->getDoctrine()->getManager()),
                        "constants" => Constants::get()
            ]);
        } else {
            $this->addFlash(Constants::FLASH_ERROR, Constants::MSG_LOGIN);
            return $this->render(Constants::VIEW_LOGIN, [
                        "constants" => Constants::get()
            ]);
        }
    }

}
