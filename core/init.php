<?php

/**
 * Function to starts sessions,
 * autoload classes and include all 
 * relevant functions.
 */

session_start();


$GLOBALS['config'] = [
    'mysql' => [
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => 'World#4938',
        'db' => 'lr'
    ],
    'remember' => [
        'cookie_name' => 'hash',
        'cookie_expiry' => 604800
    ],
    'session' => [
        'session_name' => 'user',
        'token_name' => 'token'
    ]
];

spl_autoload_register(function($class) {
    require_once 'classes/' . $class . '.php';
});

require_once 'functions/sanitize.php';

/**
* Function to log in a user who asked to
* be remembered on the site.
*/

if(Cookie::exists(Config::get('remember/cookie_name')) && !Session::exists(Config::get('session/session_name'))) {
    $hash = Cookie::get(Config::get('remember/cookie_name'));
    $hash_check = DB::getInstance()->get('users_session',['hash', '=', $hash]);

    if($hash_check->count()) {
        $user = new User($hash_check->first()->user_id);
        $user->login();
    }
}