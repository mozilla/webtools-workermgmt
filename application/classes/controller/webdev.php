<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * This controller handles the web dev forms.
 *
 * @author  skeen
 */
class Controller_Webdev extends Controller_Template {

    public function  before() {
        parent::before();
        $this->template->breadcrumbs = $this->auto_crumb();
        $this->template->main_title = "Mozilla Web Dev";
    }

    public function action_index() {
        $this->template->title = 'WebTools::Webdev Home';
        $this->template->content = new View('pages/webdev/index');
    }

    private $select_lists = null;

    public function action_project_init() {
        $hiring = new Model_Hiring($this->get_ldap());
        
        /**
         * track required fields with this array, Validator uses it and form helper
         * uses it to determine which fields to decorate as 'required' in the UI
         */
        $required_fields = array('name','overview','scope','assumptions','deliverables');
        /**
         * This should contain every field in the form (required or not)
         */
        $form = array(
            'name' => '',

            'members_it' => '',
            'members_product_driver' => '',
            'members_l10n' => '',
            'members_marketing' => '',
            'members_qa' => '',
            'members_security' => '',
            'members_webdev' => '',
            'members_other' => '',

            'overview' => '',
            'scope' => '',

            'dependencies_legal' => '',
            'dependencies_security' => '',
            'dependencies_analytics' => '',
            'dependencies_finance' => '',
            'dependencies_app' => '',
            'dependencies_other' => '',

            'assumptions' => '',
            'deliverables' => ''

        );
        $errors = $form;

        if($_POST) {
            if( ! Form::valid_token()) {
                $this->request->redirect('webdev/project_init');
            }
            Form_Helper::filter_disallowed_values($this->select_lists);
            $post = new Validate($_POST);
            // hack to have Validate keep psot key/values after ->check()
            // that did not have validation rules set (this is only needed
            // since we are not using models
            $post->labels(array_combine(array_keys($form), array_keys($form)));
            // $post->filter(true, 'trim'); //K3 filter cannot handle arrays
            $_POST = array_walk_recursive($_POST, 'trim');

            // add post rules
            // $post
            //    ->rule('start_date', 'date')
            //    ->rule('end_date', 'date')
            //    ->rule('email_address', 'email');

            // add all the required fields
            foreach ($required_fields as $required_field) {
                $post->rule($required_field, 'not_empty');
            }

            if ($post->check()) {
                $form = Arr::overwrite($form, $post->as_array());
                // File the appropriate Bugs
                $bugs_to_file = array('Webdev_Project');
                if($this->file_these($bugs_to_file, $form)) {
                    $this->request->redirect('webdev/project_init');
                }
            } else {
                $form = arr::overwrite($form, $post->as_array());
                client::validation_results(arr::overwrite(
                    $errors,
                    $post->errors('webdev_forms_validations'))
                );
                client::messageSend("There were errors in some fields", E_USER_WARNING);
            }

        }

        $memebers_autobox_groups = array(
            'members_it','members_product_driver','members_l10n',
            'members_marketing','members_qa', 'members_security',
            'members_webdev', 'members_other');
        $memebers_groups_posted = array();
        foreach ($memebers_autobox_groups as $members_group) {
            if(Arr::get($form, $members_group)) {
                $memebers_groups_posted[$members_group] = $form[$members_group];
            }
        }
        // the UI used client to determine which fields to decorate as 'required'
        form::required_fields($required_fields);
        $this->template->js_extra = HTML::script('media/js/jquery.autocomplete.min.js');
        $this->template->js_extra .= '<script type="text/javascript">var memebers_autobox_groups = '.  json_encode($memebers_autobox_groups).";\n"
                .'var memebers_groups_posted = '.  json_encode($memebers_groups_posted).'; </script>';
        $this->template->js_extra .= HTML::script('media/js/webdev.js');
        $this->template->js_extra .= HTML::script('media/js/jquery.textarearesizer.compressed.js');
        $this->template->content = new View('pages/webdev/project_init');
        $this->template->content->form = $form;
        $this->template->content->lists = $this->select_lists;
        $this->template->title = 'WebTools::Webdev Project Init';
    }
}