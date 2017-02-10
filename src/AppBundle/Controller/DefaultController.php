<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Utils\Constants;

class DefaultController extends Controller {

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request) {
        // replace this example code with whatever you need
        return $this->render('index.html.twig', array(
                "constants" => Constants::get()
            ));
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request) {
        return $this->render("login.html.twig", array(
            "constants" => Constants::get()
        ));
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

}
