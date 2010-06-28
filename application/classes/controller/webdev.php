<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * This controller handles the web dev forms.
 *
 * @author  skeen
 */
class Controller_Webdev extends Controller_Template {

    public function  before() {
        parent::before();
        $this->template->main_title = "Mozilla Corporation - Web Dev Forms";
    }

    public function action_index() {

        $this->template->title = 'WebDev::Home';
        $this->template->content = new View('pages/webdev/index');

    }
}