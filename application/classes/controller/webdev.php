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
        $this->template->main_title = "WebTools - Web Dev Forms";
    }

    public function action_index() {
        $this->template->title = 'WebTools::Webdev Home';
        $this->template->content = new View('pages/webdev/index');
    }

    private $select_lists = array(
        // this is retrieved from Manager_Model in actions that need it
        'all_employees' => array(),
    );

    public function action_project_init() {
        $hiring = new Model_Hiring($this->get_ldap());
//        $this->select_lists['manager'] = Form_Helper::format_manager_list($hiring->manager_list());
        $this->select_lists['all_employees'] = Form_Helper::format_manager_list($hiring->buddy_list());
        /**
         * track required fields with this array, Validator uses it and form helper
         * uses it to determine which fields to decorate as 'required' in the UI
         */
        $required_fields = array('name','overview','scope','assumptions','deliverables');
        /**
         * This should contain every field in the form
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
//            echo "<pre>";print_r($_POST);die;


//            if( ! Form::valid_token()) {
//                $this->request->redirect('webdev/project_init');
//            }




            Form_Helper::filter_disallowed_values($this->select_lists);
            $post = new Validate($_POST);
            // hack to have Validate keep psot key/values after ->check()
            // that did not have validation rules set (this is only needed
            // since we are not using models
            $post->labels(array_combine(array_keys($form), array_keys($form)));
            // $post->filter(true, 'trim'); //K3 filter cannot handle arrays
            $_POST = array_walk_recursive($_POST, 'trim');
            
//            $post
//                ->rule('start_date', 'date')
//                ->rule('end_date', 'date')
//                ->rule('email_address', 'email');

//            if(trim(Arr::get($_POST, 'hire_type'))=='Intern') {
//                array_push($required_fields,'end_date');
//            }
//            if(Arr::get($_POST, 'location')=='other') {
//                array_push($required_fields,'location_other');
//            }
//            if(Arr::get($_POST, 'machine_needed')=='1') {
//                array_push($required_fields,'machine_type');
//            }
            // add all the required fields
            foreach ($required_fields as $required_field) {
                $post->rule($required_field, 'not_empty');
            }

            if ($post->check()) {
                die('made it');
                // check for invilid
                $form = Arr::overwrite($form, $post->as_array());
                $form = $this->build_supplemental_form_values($form, $hiring);
                $bugs_to_file = array(Bugzilla::BUG_NEWHIRE_SETUP);
                if($form['machine_needed']) {
                    $bugs_to_file[] = Bugzilla::BUG_HARDWARE_REQUEST;
                }
                if($form['mail_needed']) {
                    $bugs_to_file[] = Bugzilla::BUG_EMAIL_SETUP;
                }
                // File the appropriate Bugs
                if($this->file_these($bugs_to_file, $form)) {
                    // Send Buddy Email
                    if( ! empty($form['buddy']) ) {
                      $this->notify_buddy($form, $hiring);
                    }
                }
                if( ! client::has_errors()) {
                    $this->request->redirect('hiring/employee');
                }

            } else {
                $form = arr::overwrite($form, $post->as_array());
                client::validation_results(arr::overwrite($errors, $post->errors('hiring_employee_form_validations')));
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
        $this->template->js_extra .= '<script type="text/javascript">var memebers_autobox_groups = '.  json_encode($memebers_autobox_groups).'; </script>';
        $this->template->js_extra .= '<script type="text/javascript">var memebers_groups_posted = '.  json_encode($memebers_groups_posted).'; </script>';
        $this->template->js_extra .= HTML::script('media/js/webdev.js');
        $this->template->js_extra .= HTML::script('media/js/jquery.textarearesizer.compressed.js');
        $this->template->css_extra = HTML::style('media/css/jquery.autocomplete.css');

//        $this->template->css_extra = HTML::style('media/css/content-assist.css');
        
        $this->template->title = 'WebTools::Webdev Project Init';
        $this->template->content = new View('pages/webdev/project_init');
        $this->template->content->form = $form;
        $this->template->content->lists = $this->select_lists;
    }
}