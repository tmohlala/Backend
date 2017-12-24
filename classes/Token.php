<?php

/**
* Class to generate a token for each refresh
* of a page to prevent Cross-site request forgery.
*/

class Token {

	/**
	* Function for generating a unique token
	* for each session.
	*/
	public static function generate() {
		return Session::put(Config::get('session/token_name'), md5(uniqid()));
	}

	/**
	* Function for checking if supplied token
	* matches current session token.
	*/

	public static function check($token) {
		$token_name = Config::get('session/token_name');
		
		if(Session::exists($token_name) && $token === Session::get($token_name)) {
			Session::delete($token_name);
			return true;
		}
		return false;
	}
}
