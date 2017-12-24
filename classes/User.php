<?php

/**
* Class for working with user
* profiles [register, login,update, check
* if user exists]
*/

class User {
	private $_db,
			$_data,
			$_session_name,
			$_cookie_name,
			$_isLoggedIn;

	public function __construct($user = null) {
		$this->_db = DB::getInstance();
		$this->_session_name =  Config::get('session/session_name');
		$this->_cookie_name =  Config::get('remember/cookie_name');


		if(!$user) {
			if(Session::exists($this->_session_name)) {
				$user = Session::get($this->_session_name);
				if($this->find($user)) {
					$this->_isLoggedIn = true;
				}
				else{
					// process logout.
				}
			}
		}
		else {
			$this->find($user);
		}
	}

	public function update($fields = [], $id = null) {

		if(!$id && $this->isLoggedIn()) {
			$id = $this->data()->id;
		}

		if(!$this->_db->update('users', $id, $fields)) {
			throw new Exception('Failed to update profile information');
		}
	}

	/**
	* Function to insert new user data
	* into the database.
	*/

	public function create($fields = []) {
		if(!$this->_db->insert('users', $fields)) {
			throw new Exception("Error: failed to create the account");
		}
	}

	/**
	* Function to find a user and
	* retrieve the user data if user exists.
	* @return true if user exists and data was retrieved
	* false otherwise.
	*/

	public function find($user = null) {
		if($user) {
			$field = (is_numeric($user)) ? 'id' : 'username';
			$data = $this->_db->get('users', [$field, '=', $user]);

			if($data->count()) {
				$this->_data = $data->first();
				return true;
			}
		}
		return false;
	}

	/**
	* Function to login a registered user
	* @return true if login was succesful and
	* false if login failed.
	*/

	public function login($username = null, $password = null, $remember = false) {
		
		if(!$username && !$password && $this->exists()) {
			Session::put($this->_session_name, $this->data()->id);
		}
		else {
			$user = $this->find($username);
			if($user) {
				if($this->data()->password === Hash::make($password, $this->data()->salt)) {
					Session::put($this->_session_name, $this->data()->id);
					if($remember) {
						$hash = Hash::unique();
						$hash_check = $this->_db->get('users_session', ['user_id', '=', $this->data()->id]);

						if(!$hash_check->count()) {
							$this->_db->insert('users_session',[
								'user_id' => $this->data()->id,
								'hash' => $hash 
							]);
						}
						else {
							$hash = $hash_check->first()->hash;
						}
						Cookie::put($this->_cookie_name, $hash, Config::get('remember/cookie_expiry'));
					}
					return true;
				}
			}
		}
		return false;
	}

	public function hasPermission($key) {
		$group = $this->_db->get('groups', ['id', '=', $this->data()->group]);
		if($group->count()) {
			 $permissions = json_decode($group->first()->permissions, true);
			 if($permissions[$key] == true) {
			 	return true;
			 }
			 else {
			 	return false;
			 }
		}
	}

	/**
	* Function to check if user exists
	* @return true if data is not empty
	* false otherwise.
	*/

	public function exists() {
		return (!empty($this->data())) ? true : false;
	}

	/**
	* Function for logging out a user.
	*/

	public function logout() {
		$this->_db->delete('users_session', ['user_id', '=', $this->data()->id]);
		Session::delete($this->_session_name);
		Cookie::delete($this->_cookie_name);
	}

	/**
	* @return data object.
	*/

	public function data() {
		return $this->_data;
	}

	/**
	* @return login checker.
	*/

	public function isLoggedIn() {
		return $this->_isLoggedIn;
	}
}
