<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\Constants;

class SecurityController extends Controller {

    /** 
     * @Route("/login", name="login")
     */
    
    public function loginAction(Request $request) {
        $authenticationUtils = $this->get('security.authentication_utils');

        /* Get the login error if there is one */
        $error = $authenticationUtils->getLastAuthenticationError();

        /* last username entered entered by the user */
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('login.html.twig', [
                    'last_username' => $lastUsername,
                    'error' => $error,
        ]);
    }
    
    /** 
     * @Route("/login_check", name="login_check")
     */
    
    public function loginCheckAction(Request $request) {
        return new Response("<html><body>RESPONSE</body></html>");
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request) {
        $request->getSession()->set("user",null);
        return $this->render("login.html.twig");
    }

    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request) {
        return $this->render("register.html.twig", array(
            "constants" => Constants::get()
        ));
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
    
    #public function onLogoutSucces(Request $request){
        #$this->addFlash("error", "You are not authenticated, please login");
        #return $this->render("login.html.twig");
    #}

}
