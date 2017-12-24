<?php

/**
* Class for user information security.
*/

class Hash {
	/**
	* Function for creating a hash key.
	* @return the hashed password string.
	*/

    public static function make($string, $salt = '') {
    	return hash('sha256', $string, $salt);
    }

    /**
    * Function for creating a salt key
    * to be combined with the password and hash 
    * for extra security.
    * @return a random string of supplied length.
    */

    public static function salt($length) {
    	return random_bytes($length);
    }

    /**
    * Function to ensure that each
    * generated hash is unique.
    * @return a unique hashed and salted password.
    */

    public static function unique() {
    	return self::make(uniqid());
    }
}
