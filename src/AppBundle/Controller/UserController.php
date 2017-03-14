<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Utils\Constants;
use AppBundle\Entity\User;
use AppBundle\Entity\User_has_Role;
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
        if ($user != null && $user->hasTheRole(Constants::USER_TYPE_ADMIN, $this->get("logger"))) {
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
        if ($user != null && $user->hasTheRole(Constants::USER_TYPE_ADMIN, $this->get("logger"))) {
            $users = $this->getAllUsersFromDB();
            return $this->render("manageUsers.html.twig", array(
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
            $resultFlag = $this->saveUserToDB($request->request->get("registerUserId"), $request->request->get("registerUserName"), $request->request->get("registerUserLastame"), $request->request->get("registerUserLogin"), $request->request->get("registerUserPassword"), $request->request->get("registerUserEmail"), $request->request->get("registerUserType"));
            if ($resultFlag == 1) {
                $this->addFlash("notice", "The user " . $request->request->get("registerUserName") . " has been registered succesfuly");
            } else {
                $this->addFlash("error", "An error ocurred, user not registered");
            }
            return $this->render("manageUsers.html.twig", [
                        "users" => $this->getAllUsersFromDB(),
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
            $userEdit = $this->getTheUserFromDB($userId);
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
            $resultFlag = $this->updateUserToDB($request->request->get("updateUserId"), $request->request->get("updateUserName"), $request->request->get("updateUserLastname"), $request->request->get("updateUserLogin"), $request->request->get("updateUserPassword"), $request->request->get("updateUserEmail"), $request->request->get("updateUserType"));
            if ($resultFlag == 1) {
                $this->addFlash(Constants::FLASH_NOTICE, Constants::MSG_USER_EDIT_PREF . $request->request->get("editUserName") . Constants::MSG_USER_EDIT_SUFF);
            } else {
                $this->addFlash(Constants::FLASH_ERROR, Constants::MSG_BAD_USER_EDIT);
            }
            return $this->render(Constants::VIEW_MANAGE_USERS, [
                        "users" => $this->getAllUsersFromDB(),
                        "constants" => Constants::get(),
            ]);
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
            $userRemoveName = $this->getTheUserFromDB($userId)->getUserName();
            $resultFlag = $this->removeUserFromDB($userId);
            if ($resultFlag == 1) {
                $this->addFlash(Constants::FLASH_NOTICE, Constants::MSG_USER_REM_PREF . $userRemoveName . Constants::MSG_USER_REM_SUFF);
            } else {
                $this->addFlash(Constants::FLASH_ERROR, Constants::MSG_BAD_USER_REM);
            }
            return $this->render(Constants::VIEW_MANAGE_USERS, [
                        "users" => $this->getAllUsersFromDB(),
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
    
    private function getAllUsersFromDB(){
        $userRepository = $this->getDoctrine()->getRepository("AppBundle:User");
        return $userRepository->findAll();
    }
    
    private function saveUserToDB($user_id, $user_name, $user_lastName, $user_login, $user_password, $user_email, $user_type){
        $roleRepository = $this->getDoctrine()->getRepository("AppBundle:Role");
        $user = new User();
        $user->setUserId($user_id);
        $user->setUserName($user_name);
        $user->setUserLastName($user_lastName);
        $user->setUserUsername($user_login);
        $user->setUserPassword(sha1($user_password));
        $user->setUserEmail($user_email);
        $user->setUserIsActive(true);
        $this->getDoctrine()->getManager()->persist($user);
        $userHasRole = new User_has_Role();
        $userHasRole->setRole($roleRepository->find($user_type));
        $userHasRole->setUser($user);
        $user->addUserHasRole($userHasRole);
        $this->getDoctrine()->getManager()->flush();
        return 1;
    }
    
    private function getTheUserFromDB($userId){
        $userRepository = $this->getDoctrine()->getRepository("AppBundle:User");
        $user = $userRepository->find($userId);
        return $user;
    }
    
    private function updateUsertoDB($user_id, $user_name, $user_lastName, $user_login, $user_password, $user_email, $user_type){
        $roleRepository = $this->getDoctrine()->getRepository("AppBundle:Role");
        $user = $this->getTheUserFromDB($user_id);
        $user->setUserName($user_name);
        $user->setUserLastName($user_lastName);
        $user->setUserUsername($user_login);
        if($user_password != ""){
            $user->setUserPassword(sha1($user_password));
        }
        $user->setUserEmail($user_email);
        $currentRole = $user->getUserHasRoles();
        if ($currentRole[0]->getRole()->getRoleId() != $user_type) {
            $this->removeAllUserRoles($user_id);
            $userHasRole = new User_has_Role();
            $userHasRole->setRole($roleRepository->find($user_type));
            $userHasRole->setUser($user);
            $user->addUserHasRole($userHasRole);
        }
        $this->getDoctrine()->getManager()->flush();
        return 1;
    }
    
    private function removeUserFromDB($userId){
        $userRepository = $this->getDoctrine()->getRepository("AppBundle:User");
        $userRemove = $userRepository->find($userId);
        $this->removeAllUserRoles($userId);
        $this->getDoctrine()->getManager()->remove($userRemove);
        $this->getDoctrine()->getManager()->flush();
        return 1;
    }
    
    private function removeAllUserRoles($userId){
        $user_has_roleRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:User_has_Role");
        $records = $user_has_roleRepository->findBy(["user" => $userId]);
        foreach($records as $record){
            $this->getDoctrine()->getManager()->remove($record);
        }
        $this->getDoctrine()->getManager()->flush();
        return 1;
    }

}
