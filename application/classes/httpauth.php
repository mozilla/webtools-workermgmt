<?php defined('SYSPATH') or die('No direct script access.');
/**
 * abstract gettin gthe HTTP auth credentials from the web server
 */
class Httpauth {

    public static function credentials() {
        $credentials = null;
        
        if($user = Arr::get($_SERVER,'PHP_AUTH_USER')) {
            $credentials = array(
                'username' => $user,
                'password' => Arr::get($_SERVER, 'PHP_AUTH_PW')
            );
        }
        return $credentials;
    }
}