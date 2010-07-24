<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Authentication Controller
 * 
 * Auths against bugzilla and stores the auth cookies to be used to submit bugs
 * for the various Hiring forms.
 *
 *
 */
class Controller_Authenticate extends Controller_Template {

    private $bugzilla_client;

    public function  __construct(Kohana_Request $request) {
        $this->bugzilla_client = new Bugzilla_Client(
            Kohana::config('workermgmt'),
            $this->httpauth_credentials()
        );
        parent::__construct($request);
    }  

    /**
     * note: Route set to /login
     */
    public function action_login() {
        $username = Arr::get($_POST,'bz_username');
        $password = Arr::get($_POST,'bz_password');
        if($_POST) {
            $post = new Validate($_POST);
            $post->filter(true, 'trim');
            $post
                ->rule('bz_username', 'not_empty')
                ->rule('bz_password', 'not_empty');

            if($post->check()) {
                if($this->bugzilla_client->login($username,$password)) {
                    $this->request->redirect('/');
                } else {
                    client::messageSend($this->bugzilla_client->error_message(), E_USER_WARNING);
                }
            } else {
                client::validation_results($post->errors('authentication'));
                client::messageSend("There were errors in some fields", E_USER_WARNING);
            }
        }
        $this->template->content = View::factory('pages/bz_login');
        $this->template->content->bz_username = $username;
        $this->template->content->bz_password = $password;
        $this->template->logout_link = '';
        $this->template->title = 'WebTools :: Login';

    }
    /**
     * note: Route set to /logout
     */
    public function action_logout() {
        $this->bugzilla_client->logout();
        client::messageSend("You have logged out", E_USER_NOTICE);
        $this->request->redirect('authenticate/login');
    }

    private function httpauth_credentials() {
        return Kohana::config('workermgmt.in_dev_mode')
            ? Httpauth::credentials()
            : null;
    }

}