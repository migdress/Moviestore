<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;

class DefaultController extends Controller {
	/**
	 * @Route("/", name="homepage")
	 */
	public function indexAction(Request $request) {
		// replace this example code with whatever you need
		return $this->render ( 'index.html.twig', [ 
				'base_dir' => realpath ( $this->getParameter ( 'kernel.root_dir' ) . '/..' ) . DIRECTORY_SEPARATOR 
		] );
	}
	
	/**
	 * @Route("/login", name="login")
	 */
	public function loginAction(Request $request) {
		return $this->render ( "login.html.twig" );
	}
        
        /**
	 * @Route("/register", name="register")
	 */
	public function registerAction(Request $request) {
		return $this->render ( "register.html.twig" );
	}
        
	
	/**
	 * @Route("/loginAttempt", name="loginAttempt")
	 */
	public function loginAttemptAction(Request $request) {
		$userLogin = $request->request->get ( "login" );
		$userPassword = $request->request->get ( "password" );
		
		$user = $this->login ( $userLogin, $userPassword );
		
		if ($user) {
			$request->getSession ()->set ( "user", $user );
			$type = $user->getUserType ();
			
			if ($type == "ADMIN") {
				return $this->render ( "admin.html.twig", array (
						"user" => $user 
				) );
			} else {
				return $this->render ( "account.html.twig", array (
						"user" => $user 
				) );
			}
		} else {
			$this->addFlash ( "error", "Invalid data" );
			return $this->render ( "login.html.twig" );
		}
		return $this->render ( "login.html.twig" );
	}
	
	/* Login method */
	public function login($userLogin, $userPassword) {
		$logger = $this->container->get ( "logger" );
		
		/* Preparing repositories */
		$userRepository = $this->getDoctrine ()->getRepository ( "AppBundle:User" );
		
		/* Trying to find de User */
		if ($user = $userRepository->findOneBy ( array (
				"user_login" => $userLogin 
		) )) {
			if ($user->getUserPassword () == sha1 ( $userPassword )) {
				$logger->info ( "Login success" );
				$return = $user;
				return $return;
			} else {
				return null;
			}
		} else {
			return null;
		}
	}
}
