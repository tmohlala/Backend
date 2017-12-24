<?php

/**
* Class to manage cookies.
*/

class Cookie {

	/**
	* Function to check if a cookie exist.
	* @return true if cookie exists false otherwise.
	*/

    public static function exists($name) {
    	return (isset($_COOKIE[$name])) ? true : false;
    }

    /**
    * Function to get a set cookie.
    * @return the set cookie.
    */

    public static function get($name) {
    	return $_COOKIE[$name];
    }

    /**
    * Function to set/reset cookies
    * @return true if setting was succesful and
    * false on failure.
    */

    public static function put($name, $value, $expiry) {
    	if(setcookie($name, $value, time() + $expiry, '/')) {
    		return true;
    	}
    	return false;
    }

    /**
    * Function to delete a set cookie.
    */

    public static function delete($name) {
    	self::put($name, '', time() - 1);
    }
}
