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
        $bugzilla_connector = Bugzilla::instance(kohana::config('workermgmt'));
        $filing = array();
        foreach ($bugs_to_file as $bug_to_file) {
            try {
                $filing = Filing::factory(
                        $bug_to_file, $form_input, $bugzilla_connector)
                        ->file();
                
                client::messageSend($filing['success_message'], E_USER_NOTICE);
                $success = true;
            } catch (Exception $e) {
                /**
                 * either the supplied $submitted_data to the Filing instance
                 * was missing or contruct_content() method of the Filing
                 * instance tried to access a submitted content key that did
                 * not exist.
                 */
                if($e->getCode()==Filing::EXCEPTION_MISSING_INPUT) {
                    Kohana_Log::instance()->log->add('error',__METHOD__." {$e->getMessage()}");
                    Client::messageSend('Missing required input to build this Bug', E_USER_ERROR);
                /**
                 * bug was constructed successfully but we got an error back
                 * when we sent it to Bugzilla
                 */
                } else if($e->getCode()==Filing::EXCEPTION_BUGZILLA_INTERACTION) {
                    Kohana_Log::instance()->add('error',__METHOD__." {$e->getMessage()}");
                    Client::messageSend("There was an error communicating "
                        ."with the Bugzilla server: {$e->getMessage()}", E_USER_ERROR);
                /**
                 * something happend, log it and toss it
                 */
                } else {
                    Kohana_Log::instance()->add('error',__METHOD__." {$e->getMessage()}\n{$e->getTraceAsString()}");
                    Client::messageSend('Unknown exception when filing this bug', E_USER_ERROR);
                    throw $e;
                }
            }
        }
        return $success;
    }
}