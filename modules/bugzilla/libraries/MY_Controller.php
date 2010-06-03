<?php defined('SYSPATH') or die('No direct script access.');

class Controller extends Controller_Core {

    private $non_authed_areas = array(
        'authenticate::login',
        'authenticate::logout'
    );

    public function __construct() {
        parent::__construct();

        Kohana::config_load('workermgmt');
        
        $this->profiler = IN_DEV_MODE ? new Profiler : null;
        
        $requested_area = strtolower(Router::$controller."::".Router::$method);
        if( ! in_array($requested_area, $this->non_authed_areas)) {
            // run authentication
            if( ! Bugzilla::instance(Kohana::config('workermgmt'))->authenticated()) {
                url::redirect('login');
            }
        }

    }
    protected function get_ldap() {
        if( IN_DEV_MODE && kohana::config('workermgmt.use_mock_ldap')) {
          $ldap = new Mock_Ldap(kohana::config('workermgmt'), $this->ldap_credentials());
        } else {
          $ldap = new Ldap(kohana::config('workermgmt'), $this->ldap_credentials());
        }
        return $ldap;
    }
    private function ldap_credentials() {
        return array(
            'username' => isset ($_SERVER['PHP_AUTH_USER'])?$_SERVER['PHP_AUTH_USER']:null,
            'password' => isset ($_SERVER['PHP_AUTH_PW'])?$_SERVER['PHP_AUTH_PW']:null
        );
    }
}