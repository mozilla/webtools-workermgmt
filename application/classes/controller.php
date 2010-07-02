<?php defined('SYSPATH') or die('No direct script access.');

class Controller extends Kohana_Controller {

    private $non_authed_areas = array(
        'authenticate::login',
        'authenticate::logout'
    );

    //                                    label        path
    protected $static_crumb_base = array('web forms' => '/');

    public function __construct(Kohana_Request $request) {
        parent::__construct($request);
        
        $requested_area = strtolower($this->request->controller."::".$this->request->action);
        if( ! in_array($requested_area, $this->non_authed_areas)) {
            // run authentication
            if( ! Bugzilla::instance(Kohana::config('workermgmt'))->authenticated()) {
                $this->request->redirect('authenticate/login');
            }
        }

    }
    protected function get_ldap() {
        if( kohana::config('workermgmt.in_dev_mode') && kohana::config('workermgmt.use_mock_ldap')) {
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


    /**
     * works well for structured portions of the site (like admin interfaces)
     *
     * @return array
     */
    protected function auto_crumb() {

        $crumbs = isset($this->static_crumb_base)&&$this->static_crumb_base
                ?array($this->static_crumb_base)
                :array();
        /*
         * build | base / controller / action
         */
        $crumb_base_path = current($this->static_crumb_base);
        $crumb_base_path == '/' ? '' : $crumb_base_path;
        if(!empty($this->request->controller)) {
            $controller_path = "{$crumb_base_path}/{$this->request->controller}";
            array_push($crumbs, array(str_replace('_', " ", $this->request->controller) => $controller_path));
        }
        if(!empty($this->request->action) && strtolower($this->request->action) !='index' ) {
            $action_path = "{$controller_path}/{$this->request->action}";
            array_push($crumbs, array(str_replace('_', " ", $this->request->action) => $action_path));
        }
        // de-link the tail
        array_push($crumbs,array(key(array_pop($crumbs))));
        return $crumbs;
    }

    /**
     * Submit these bug types using the validated from data
     *
     * @param array $bugs_to_file Must be known values of Bugzilla
     *      i.e. Bugzilla::BUG_NEWHIRE_SETUP, Bugzilla::BUG_HR_CONTRACTOR, ...
     * @param array $form_input The validated form input
     */
    protected function file_these(array $bugs_to_file, $form_input) {
        $success = false;
        $bugzilla = Bugzilla::instance(kohana::config('workermgmt'));
        foreach ($bugs_to_file as $bug_to_file) {
            $filing = $bugzilla->newhire_filing($bug_to_file, $form_input);
            if ($filing['error_message']!==null) {
                client::messageSend($filing['error_message'], E_USER_ERROR);
            } else {
                client::messageSend($filing['success_message'], E_USER_NOTICE);
                $success = true;
            }
        }
        return $success;
    }
}