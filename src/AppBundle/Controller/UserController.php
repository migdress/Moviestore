<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Utils\Constants;
use AppBundle\Entity\User;
use AppBundle\Entity\Movie;
use AppBundle\Entity\Purchase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class UserController extends Controller {

    /**
     * @Route("/loginSuccess", name="loginSuccess")
     */
    public function loginSuccessAction(Request $request) {
        $user = $this->validateSession();
        $pageToRender = "";
        if ($user != null && $user->hasTheRole(Constants::USER_TYPE_ADMIN)) {
            $pageToRender = "admin.html.twig";
        } else if ($user != null && $user->hasTheRole(Constants::USER_TYPE_CLIENT)) {
            $pageToRender = "account.html.twig";
        } else {
            $this->addFlash("error", "You are not authenticated, please login");
            return $this->redirectToRoute("logout");
        }
        return $this->render($pageToRender, [
                    "constants" => Constants::get()
        ]);
    }

    public function validateSession() {
        /* This line allows to check if the user is authenticated */
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            /* The line below should return the user object if authenticated */
            #$userSession = $this->get('security.token_storage')->getToken()->getUser();
            $userSession = $this->getUser(); #this line is a shortcut for the one commented above
            if ($userSession != null) {
                $userSession->loadRoles($this->getDoctrine()->getManager());
                return $userSession;
            } else {
                return null;
            }
        } else {
            throw $this->createAccessDeniedException();
        }
    }

    #/**
    # * @Route("/loginAttempt", name="loginAttempt")
    # */
    #public function loginAttemptAction(Request $request) {
    #    $userLogin = $request->request->get("login");
    #    $userPassword = $request->request->get("password");
    #    $user = User::login($userLogin, $userPassword, $this->getDoctrine()->getManager());
    #    $pageToRender = "";
    #    if ($user) {
    #        $request->getSession()->set("user", $user);
    #        if ($user->getUserType() == Constants::USER_TYPE_ADMIN) {
    #            $pageToRender = "admin.html.twig";
    #        } else {
    #           $pageToRender = "account.html.twig";
    #        }
    #        return $this->render($pageToRender, array(
    #                    "user" => $user,
    #                    "constants" => Constants::get()
    #        ));
    #    } else {
    #        $this->addFlash("error", "Invalid data");
    #        return $this->render("login.html.twig", [
    #                    "constants" => Constants::get()
    #        ]);
    #    }
    #}

    /**
     * @Route("/admin", name="admin")
     */
    public function adminAction(Request $request) {
        $user = $this->validateSession();
        if ($user != null && $user->hasTheRole(Constants::USER_TYPE_ADMIN)) {
            return $this->render("admin.html.twig", array(
                        "user" => $user,
                        "constants" => Constants::get()
            ));
        } else {
            $this->addFlash("error", "You are not authenticated, please login");
            return $this->redirectToRoute("logout");
        }
    }

    /**
     * @Route("/account", name="account")
     */
    public function accountAction(Request $request) {
        $user = $this->validateSession();
        if ($user != null && $user->hasTheRole(Constants::USER_TYPE_CLIENT)) {
            return $this->render("account.html.twig", [
                        "user" => $user,
                        "constants" => Constants::get()
            ]);
        } else {
            $this->addFlash("error", "You are not authenticated, please login");
            return $this->redirectToRoute("logout");
        }
    }

    /**
     * @Route("/manageUsers", name="manageUsers")
     */
    public function manageUsersAction(Request $request) {
        $user = $this->validateSession();
        if ($user != null && $user->hasTheRole(Constants::USER_TYPE_ADMIN)) {
            $users = User::getAllUsers($this->getDoctrine()->getManager());
            return $this->render("manageUsers.html.twig", array(
                        "user" => $user,
                        "users" => $users,
                        "constants" => Constants::get()
            ));
        } else {
            $this->addFlash("error", "You are not authenticated, please login");
            return $this->redirectToRoute("logout");
        }
    }

    /**
     * @Route("/registerUser", name="registerUser")
     */
    public function registerUserAction(Request $request) {
        $user = $this->validateSession();
        if ($user != null && $user->hasTheRole(Constants::USER_TYPE_ADMIN)) {
            $resultFlag = User::register($request->request->get("registerUserId"), $request->request->get("registerUserName"), $request->request->get("registerUserLastame"), $request->request->get("registerUserLogin"), $request->request->get("registerUserPassword"), $request->request->get("registerUserEmail"), $request->request->get("registerUserType"), $this->getDoctrine()->getManager());
            if ($resultFlag == 1) {
                $this->addFlash("notice", "The user " . $request->request->get("registerUserName") . " has been registered succesfuly");
            } else {
                $this->addFlash("error", "An error ocurred, user not registered");
            }
            return $this->render("manageUsers.html.twig", [
                        "user" => $user,
                        "users" => User::getAllUsers($this->getDoctrine()->getManager()),
                        "constants" => Constants::get()
            ]);
        } else {
            $this->addFlash("error", "You are not authenticated, please login");
            return $this->redirectToRoute("logout");
        }
    }

    /**
     * @Route("/editUser/{userId}", name="editUser", requirements={"userId": "\d+"})
     */
    public function editUserAction(Request $request, $userId) {
        $user = $this->validateSession();
        if ($user != null && $user->hasTheRole(Constants::USER_TYPE_ADMIN)) {
            $userEdit = User::getTheUser($userId, $this->getDoctrine()->getManager());
            return $this->render(Constants::VIEW_EDIT_USER, [
                        "constants" => Constants::get(),
                        "user" => $userEdit]);
        } else {
            $this->addFlash("error", "You are not authenticated, please login");
            return $this->redirectToRoute("logout");
        }
    }

    /**
     * @Route("/updateUser", name="updateUser")
     */
    public function updateUserAction(Request $request) {
        $user = $this->validateSession();
        if ($user != null && $user->hasTheRole(Constants::USER_TYPE_ADMIN)) {
            $resultFlag = User::update($request->request->get("updateUserId"), $request->request->get("updateUserName"), $request->request->get("updateUserLastname"), $request->request->get("updateUserLogin"), $request->request->get("updateUserPassword"), $request->request->get("updateUserType"), $this->getDoctrine()->getManager());
            if ($resultFlag == 1) {
                $this->addFlash(Constants::FLASH_NOTICE, Constants::MSG_USER_EDIT_PREF . $request->request->get("editUserName") . Constants::MSG_USER_EDIT_SUFF);
            } else {
                $this->addFlash(Constants::FLASH_ERROR, Constants::MSG_BAD_USER_EDIT);
            }
            return $this->render(Constants::VIEW_MANAGE_USERS, ["constants" => Constants::get(), "users" => User::getAllUsers($this->getDoctrine()->getManager())]);
        } else {
            $this->addFlash("error", "You are not authenticated, please login");
            return $this->redirectToRoute("logout");
        }
    }

    /**
     * @Route("/removeUser/{userId}", name="removeUser", requirements={"userId": "\d+"})
     */
    public function removeUserAction(Request $request, $userId) {
        $user = $this->validateSession();
        if ($user != null && $user->hasTheRole(Constants::USER_TYPE_ADMIN)) {
            $userRemoveName = User::getTheUser($userId, $this->getDoctrine()->getManager())->getUserName();
            $resultFlag = User::remove($userId, $this->getDoctrine()->getManager());
            if ($resultFlag == 1) {
                $this->addFlash(Constants::FLASH_NOTICE, Constants::MSG_USER_REM_PREF . $userRemoveName . Constants::MSG_USER_REM_SUFF);
            } else {
                $this->addFlash(Constants::FLASH_ERROR, Constants::MSG_BAD_USER_REM);
            }
            return $this->render(Constants::VIEW_MANAGE_USERS, [
                        "users" => User::getAllUsers($this->getDoctrine()->getManager()),
                        "constants" => Constants::get()
            ]);
        } else {
            $this->addFlash("error", "You are not authenticated, please login");
            return $this->redirectToRoute("logout");
        }
    }

    /**
     * @Route("/userRentals", name="userRentals")
     */
    public function searchAction(Request $request) {
        $user = $request->getSession()->get("user");
        $rentals = $this->getAllRentals();
        $movies = $this->getAllMovies();
        $userRentals = array();
        $iterator = 0;
        for ($i = 0; $i < count($rentals); $i++) {
            if ($user->getUserId() == $rentals[$i]->getUserId()) {
                $userRentals[$iterator] = $rentals[$i];
            }
        }


        return $this->render("userRentals.html.twig", array(
                    "user" => $user,
                    "movies" => $movies,
                    "rentals" => $rentals,
                    "userRentals" => $userRentals,
        ));
    }

    /**
     * @Route("/rentMovie/{userId}{movieId}", name="rentMovie")
     */
    public function rentMovieAction(Request $request, $userId, $movieId) {
        $logger = $this->container->get("logger");
        $user = $request->getSession()->get("user");
        $rented = false;

        $rentals = $this->getAllRentals();
        for ($i = 0; $i < count($rentals); $i++) {
            if ($movieId == $rentals[$i]->getMovieId() && $rentals[$i]->getRentalStatus() == "VALID") {
                $rented = true;
                $users = $this->getAllUsers();
                $rentals = $this->getAllRentals();
                $this->addFlash("error", "The movie is already rented");
                return $this->render("search.html.twig", array(
                            "user" => $user,
                            "users" => $users,
                            "rentals" => $rentals,
                ));
            }
        }
        if (!$rented) {
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
            return $this->render("search.html.twig", array(
                        "user" => $user,
                        "users" => $users,
                        "rentals" => $rentals,
            ));
        }
    }

    /**
     * @Route("/retrieveMovie/{userId}{movieId}", name="rentMovie")
     */
    public function retrieveMovieAction(Request $request, $userId, $movieId) {
        $logger = $this->container->get("logger");
        $user = $request->getSession()->get("user");
        $rented = false;

        $rentals = $this->getAllRentals();
        for ($i = 0; $i < count($rentals); $i++) {
            if ($movieId == $rentals[$i]->getMovieId() && $rentals[$i]->getRentalStatus() == "VALID") {
                $rented = true;
                $users = $this->getAllUsers();
                $rentals = $this->getAllRentals();
                $this->addFlash("error", "The movie is already rented");
                return $this->render("search.html.twig", array(
                            "user" => $user,
                            "users" => $users,
                            "rentals" => $rentals,
                ));
            }
        }
        if (!$rented) {
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
            return $this->render("search.html.twig", array(
                        "user" => $user,
                        "users" => $users,
                        "rentals" => $rentals,
            ));
        }
    }

}
