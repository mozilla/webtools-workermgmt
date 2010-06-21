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

    public function  __construct(Kohana_Request $request) {
        $this->bugzilla = Bugzilla::instance(Kohana::config('workermgmt'));
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
            $post->filter(TRUE, 'trim');
            $post
                ->rule('bz_username', 'not_empty')
                ->rule('bz_password', 'not_empty');

            if($post->check()) {
                if($this->bugzilla->login($username,$password)) {
                    $this->request->redirect('/');
                } else {
                    client::messageSend($this->bugzilla->error_message(), E_USER_WARNING);
                }
            } else {
                client::validation_results($post->errors());
                client::messageSend("There were errors in some fields", E_USER_WARNING);
            }
        }
        $this->template->content = View::factory('pages/bz_login');
        $this->template->content->bz_username = $username;
        $this->template->content->bz_password = $password;
        $this->template->title = 'Worker Managment :: Login';

    }
    /**
     * note: Route set to /logout
     */
    public function action_logout() {
        $this->bugzilla->logout();
        client::messageSend("You have logged out", E_USER_NOTICE);
        $this->request->redirect('authenticate/login');
    }

}