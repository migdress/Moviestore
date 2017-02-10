<?php
namespace AppBundle\Utils;

class Constants {
    
    
    
    const USER_TYPE_ADMIN = "ADMIN";
    const USER_TYPE_CLIENT = "CLIENT";
    
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
