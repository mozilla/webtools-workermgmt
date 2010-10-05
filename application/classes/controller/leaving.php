<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Controller handling processes of leaving the company
 * @author Alexander Podgorny
 */


class Controller_Leaving extends Controller_Filing {
    
    public function action_index() {
        $model_hiring                   = new Model_Hiring(Ldap::instance());
        $this->select_lists['manager']  = Form_Helper::format_manager_list($model_hiring->manager_list(true));
        /*
            - reason for leaving
            - manager (from LDAP on date of filing)
            - date of notice given
            - last day of work
            - equipment to be returned (IT inventory can tell us this)
            - send notification to current manager, erica and karen
        */
        $form = array(
            'full_name'             => '',
            'reason_for_leaving'    => '',
            'manager'               => '',
            'date_of_notice'        => '',
            'date_of_last_day'      => '',
            'equipment_to_return'   => '',
            'do_notify_managers'    => '',
            'start_date'            => ''
        );
        $required_fields = array(
            'full_name',
            'reason_for_leaving',
            'manager',
            'date_of_notice',
            'date_of_last_day'
        );
        $errors = $form;
        
        if ($_POST) {
            if (!Form::valid_token()) {
                $this->request->redirect('leaving/index');
            }
            Form_Helper::filter_disallowed_values($this->select_lists);
            $post = new Validate($_POST);
            // hack to have Validate keep post key/values after ->check()
            // that did not have validation rules set (this is only needed
            // since we are not using models
            $post->labels(array_combine(array_keys($form), array_keys($form)));
            $post->filter(true, 'trim');
            $post
                ->rule('date_of_notice',   'date')
                ->rule('date_of_last_day', 'date');
            
            // add all the required fields
            foreach ($required_fields as $required_field) {
                $post->rule($required_field, 'not_empty');
            }

            // check for invalid
            if ($post->check()) {
                $form = Arr::overwrite($form, $post->as_array());
                $bugs_to_file = array('Leaving_Setup');
                
                // File the appropriate Bugs
                if ($this->file_these($bugs_to_file, $form)) {
                    $this->request->redirect('leaving/index');
                }
            } else {
                $form = arr::overwrite($form, $post->as_array());
                client::validation_results(arr::overwrite(
                    $errors,
                    $post->errors('hiring_forms_validations'))
                );
                client::messageSend("There were errors in some fields", E_USER_WARNING);
            }

        }
        form::required_fields($required_fields);
        $this->template->title          = 'WebTools::Employment termination request';
        $this->template->js_extra       = HTML::script('media/js/jquery.autocomplete.min.js');
        $this->template->content        = View::factory('pages/leaving/index');
        $this->template->content->form  = $form;
        $this->template->content->lists = $this->select_lists;
    }
    
}