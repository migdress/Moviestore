<?php
namespace AppBundle\Utils;

class Constants {
    
    /*User types*/
    const USER_TYPE_ADMIN = "ADMIN";
    const USER_TYPE_CLIENT = "CLIENT";
    
    /*Flash message types*/
    const FLASH_ERROR = "error";
    const FLASH_NOTICE = "notice";
    
    /*Messages*/
    const MSG_LOGIN = "Please login to your account";
    
    const MSG_USER_EDIT_PREF = "The user ";
    const MSG_USER_EDIT_SUFF = " was updated succesfuly";
    const MSG_MOVIE_EDIT_PREF = "The move ";
    const MSG_MOVIE_EDIT_SUFF = " was updated succesfuly";
    const MSG_USER_REG_PREF = "The user ";
    const MSG_USER_REG_SUFF = " was registered succesfuly";
    const MSG_MOVIE_REG_PREF = "The movie ";
    const MSG_MOVIE_REG_SUFF = " was registered succesfuly";
    const MSG_USER_REM_PREF = "The user ";
    const MSG_USER_REM_SUFF = " was removed succesfuly";
    const MSG_MOVIE_REM_PREF = "The movie ";
    const MSG_MOVIE_REM_SUFF = " was removed succesfuly";
    
    const MSG_BAD_USER_EDIT = "An error has ocurred, the user was not updated";
    const MSG_BAD_MOVIE_EDIT = "An error has ocurred, the movie was not updated";
    const MSG_BAD_USER_REG = "An error has ocurred, the user was not registered";
    const MSG_BAD_MOVIE_REG = "An error has ocurred, the movie was not registered";
    const MSG_BAD_USER_REM = "An error has ocurred, the user was not removed";
    const MSG_BAD_MOVIE_REM = "An error has ocurred, the movie was not removed";
    
    /*Views*/
    const VIEW_LOGIN = "login.html.twig";
    const VIEW_MANAGE_USERS = "manageUsers.html.twig";
    const VIEW_MANAGE_MOVIES = "manageMovies.html.twig";
    const VIEW_EDIT_USER = "editingUser.html.twig";
    
    
    public static function get(){
        static $thisOne;        
        if(!$thisOne){
                $thisOne = new Constants();
            return $thisOne;
        }else{
            return $thisOne;
        }
    }
}
