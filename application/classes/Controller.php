<?php defined('SYSPATH') or die('No direct script access.');

class Controller extends Kohana_Controller {

    private $non_authed_areas = array(
        'authenticate::login',
        'authenticate::logout'
    );

    public function __construct(Kohana_Request $request) {
        parent::__construct($request);
        
//        $this->profiler = IN_DEV_MODE ? new Profiler : null;
        
        $requested_area = strtolower($this->request->controller."::".$this->request->action);
        if( ! in_array($requested_area, $this->non_authed_areas)) {
            // run authentication
            if( ! Bugzilla::instance(Kohana::config('workermgmt'))->authenticated()) {
                $this->request->redirect('authenticate/login');
            }
        }

    }
    protected function get_ldap() {
        if( IN_DEV_MODE && kohana::config('workermgmt.use_mock_ldap')) {
          $ldap = new Ldap_Mock(kohana::config('workermgmt'), $this->ldap_credentials());
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