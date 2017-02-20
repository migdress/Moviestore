<?php
namespace AppBundle\Utils;

class Constants {
    
    /* Directories */
    const DIR_MOVIE_IMAGES = "images/movies";
    
    /*User types*/
    const USER_TYPE_ADMIN = "ADMIN";
    const USER_TYPE_CLIENT = "CLIENT";
    
    /*Flash message types*/
    const FLASH_ERROR = "error";
    const FLASH_NOTICE = "notice";
    
    /*Messages*/
    const MSG_LOGIN = "Please login to your account";
    const MSG_ACCESS_DENIED = "You are not allowed to be here!";
    
    const MSG_USER_EDIT_PREF = "The user ";
    const MSG_USER_EDIT_SUFF = " was updated succesfuly";
    const MSG_GENRE_EDIT_PREF = "The genre ";
    const MSG_GENRE_EDIT_SUFF = " was updated succesfuly";
    const MSG_MOVIE_EDIT_PREF = "The movie ";
    const MSG_MOVIE_EDIT_SUFF = " was updated succesfuly";
    const MSG_USER_REG_PREF = "The user ";
    const MSG_USER_REG_SUFF = " was registered succesfuly";
    const MSG_GENRE_REG_PREF = "The genre ";
    const MSG_GENRE_REG_SUFF = " was registered succesfuly";
    const MSG_MOVIE_REG_PREF = "The movie ";
    const MSG_MOVIE_REG_SUFF = " was registered succesfuly";
    const MSG_USER_REM_PREF = "The user ";
    const MSG_USER_REM_SUFF = " was removed succesfuly";
    const MSG_GENRE_REM_PREF = "The genre ";
    const MSG_GENRE_REM_SUFF = " was removed succesfuly";
    const MSG_MOVIE_REM_PREF = "The movie ";
    const MSG_MOVIE_REM_SUFF = " was removed succesfuly";
    
    const MSG_BAD_USER_EDIT = "An error has ocurred, the user was not updated";
    const MSG_BAD_GENRE_EDIT = "An error has ocurred, the genre was not updated";
    const MSG_BAD_MOVIE_EDIT = "An error has ocurred, the movie was not updated";
    const MSG_BAD_USER_REG = "An error has ocurred, the user was not registered";
    const MSG_BAD_GENRE_REG = "An error has ocurred, the genre was not registered";
    const MSG_BAD_MOVIE_REG = "An error has ocurred, the movie was not registered";
    const MSG_BAD_USER_REM = "An error has ocurred, the user was not removed";
    const MSG_BAD_GENRE_REM = "An error has ocurred, the genre was not removed";
    const MSG_BAD_MOVIE_REM = "An error has ocurred, the movie was not removed";
    
    /*Views*/
    const VIEW_LOGIN = "login.html.twig";
    const VIEW_MANAGE_USERS = "manageUsers.html.twig";
    const VIEW_MANAGE_GENRES = "manageGenres.html.twig";
    const VIEW_MANAGE_MOVIES = "manageMovies.html.twig";
    const VIEW_EDIT_USER = "editingUser.html.twig";
    const VIEW_EDIT_GENRE = "editingGenre.html.twig";
    const VIEW_EDIT_MOVIE = "editingMovie.html.twig";
    
    
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
