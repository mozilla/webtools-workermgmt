<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * This controller handles the web dev forms.
 *
 * @author  skeen
 */
class Controller_Home extends Controller_Template {


    /**
     * Landing page
     */
    public function action_index() {
        $this->template->title = 'WebTools::Home';
        $this->template->content = new View('pages/home/index');

    }
}