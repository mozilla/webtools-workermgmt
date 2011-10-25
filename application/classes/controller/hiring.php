<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * This controller handles the hiring forms.
 *
 * @author     Sam Keen
 */
class Controller_Hiring extends Controller_Filing {

    public function  before() {
        parent::before();
        $this->template->breadcrumbs = $this->auto_crumb();
        $this->template->main_title = "Mozilla Corporation - Hiring Forms";
    }

    public function action_index() {
        $this->template->title = 'WebTools::New Hire Home';
        $this->template->content = new View('pages/hiring/index');
    }

    /**
     * No DB for this app, so these are various lookup lists needed for form
     * select list, radio groups and such
     *
     * *REQUIRED: the key for the list needs to be the name for the id used
     * in the form
     * 
     */
    private $select_lists = array(
        'employee_type' 	=> array(
            ''  	   				=> 'Select ...',
            'Employee' 				=> 'Employee',
			'Seasonal' 				=> 'Seasonal',
            'Intern'   				=> 'Intern',
        ),
        // this is retrieved from Manager_Model in actions that need it
        'manager' 			=> array(),
        'buddy' 			=> array(),
        'location' 			=> array(
            ''  					=> 'Select ...',
            'Mountain View' 		=> 'Mountain View',
            'Auckland' 				=> 'Auckland',
            'Beijing' 				=> 'Beijing',
            'Copenhagen' 			=> 'Copenhagen',
            'Paris' 				=> 'Paris',
            'Toronto' 				=> 'Toronto',
            'Vancouver' 			=> 'Vancouver',
            'other' 				=> 'other'
        ),
        'machine_type' 		=> array(
            ''       				=> 'Please select...',
            'MacBook Pro 13-inch' 	=> 'MacBook Pro 13-inch',
            'MacBook Pro 15-inch' 	=> 'MacBook Pro 15-inch',
            'Lenovo' 				=> 'Lenovo'
        ),
        'contract_type' 	=> array(
			'Extension'				=>	'Extension',
			'New'					=>	'New'
		),
        'contract_category' => array(
			'Independent'			=>	'Independent',
			'Corp to Corp'			=>	'Corp to Corp'
		),
        'hire_type' => array(
			'None'					=>	'None',
			'Contractor'			=>	'Contractor',
			'Intern'				=>	'Intern',
			'Re-hire'				=>	'Re-hire'
		)
    );

    /**
     * Form for hiring Employee and Interns
     */
    public function action_employee() {
        $hiring = new Model_Hiring(Ldap::instance());
        $this->select_lists['manager'] 	= Form_Helper::format_manager_list($hiring->all_emps_list());
        $this->select_lists['buddy'] 	= Form_Helper::format_manager_list($hiring->all_emps_list());
		$this->select_lists['country'] 	= Form_Helper::format_country_list($hiring->country_currency_list(), false);
        /**
         * track required fields with this array, Validator uses it and form helper
         * uses it to determine which fields to decorate as 'required' in the UI
         */

        $required_fields = array(
			'employee_type',
			'first_name',
			'last_name',
			'email_address',
			'start_date',
			'manager',
			'position_title',
			'department',
		//	'buddy',
			
			'address_country',
			//'address_province',
			'address_city',
			'address_street',
			'address_postal_code',
			'office_contact'
		);
        /**
         * This should contain every field in the form (required or not)
         */
        $form = array(
            'employee_type' 		=> '',
            'first_name' 			=> '',
            'last_name' 			=> '',
            'email_address' 		=> '',
            'manager' 				=> '',
            'buddy' 				=> '',
			'notification_email'	=> '',

			'position_title' 		=> '',
			'department' 			=> '',
			'start_date' 			=> '',
            'end_date' 				=> '',
			'hire_type' 			=> '',
			'immigration' 			=> '',
			'relocation' 			=> '',

			'address_country' 		=> '',
			'address_province' 		=> '',
			'address_city' 			=> '',
			'address_street' 		=> '',
			'address_postal_code'	=> '',
			
            'office_contact' 		=> '',
            'location_other' 		=> '',
            'mail_needed' 			=> '',
            'default_username' 		=> '',
            'mail_alias' 			=> '',
            'mail_lists' 			=> '',
            'other_comments' 		=> '',
            'machine_needed' 		=> '',
            'machine_type' 			=> '',
            'machine_special_requests' => '',
        );
        $errors = $form;

        if ($_POST) {
            if ( ! Form::valid_token()) {
                $this->request->redirect('hiring/employee');
            }
            Form_Helper::filter_disallowed_values($this->select_lists);
            $post = new Validate($_POST);
            // hack to have Validate keep psot key/values after ->check()
            // that did not have validation rules set (this is only needed
            // since we are not using models
            $post->labels(array_combine(array_keys($form), array_keys($form)));
            $post->filter(true, 'trim');
            $post
                ->rule('start_date', 	'date')
                ->rule('end_date', 		'date')
                ->rule('email_address', 'email');
            
            if (trim(Arr::get($_POST, 'employee_type')) == 'Intern') {
                array_push($required_fields,'end_date');
            }
			if (trim(Arr::get($_POST, 'employee_type')) == 'Seasonal') {
                array_push($required_fields,'end_date');
            }
            if (Arr::get($_POST, 'machine_needed') == '1') {
                array_push($required_fields,'machine_type');
            }
            // add all the required fields
            foreach ($required_fields as $required_field) {
                $post->rule($required_field, 'not_empty');
            }
            if ($post->check()) {
                // check for invilid
                $form = Arr::overwrite($form, $post->as_array());
                $form = $this->build_supplemental_form_values($form, $hiring);
                $bugs_to_file = array('Newhire_Setup');

                if ($form['machine_needed']) {
                    $bugs_to_file[] = 'Newhire_Hardware';
                }
                if ($form['mail_needed']) {
                    $bugs_to_file[] = 'Newhire_Email';
                }
                // File the appropriate Bugs
				if ($aBugsFiled = $this->file_these($bugs_to_file, $form)) {
                    // Send Buddy Email
                    if ( ! empty($form['buddy']) ) {
                      $this->notify_buddy($form, $hiring);
                    }
					if ($form['notification_email'] == '1') {
						$this->send_newhire_notification($form, $bugs_to_file, $aBugsFiled);
					}
                    $this->request->redirect('hiring/employee');
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
        // the UI used client to determine which fields to decorate as 'required'
        form::required_fields($required_fields);
        $this->template->js_extra = HTML::script('media/js/jquery.autocomplete.min.js');
        $this->template->title = 'WebTools::Employee New Hire';
        $this->template->content = new View('pages/hiring/employee');
        $this->template->content->form = $form;
        $this->template->content->lists = $this->select_lists;
    }
    /**
     * Form for submitting Contractor hirings
     */
    public function action_contractor() {
        $hiring = new Model_Hiring(Ldap::instance());
        $this->select_lists['manager'] 			= Form_Helper::format_manager_list($hiring->all_emps_list());
        $this->select_lists['buddy'] 			= Form_Helper::format_manager_list($hiring->all_emps_list());
		$this->select_lists['country'] 			= Form_Helper::format_country_list($hiring->country_currency_list());
		$this->select_lists['country_currency'] = $hiring->country_currency_list();
		
        // allow only employee_type = 'Contractor'
        $this->select_lists['employee_type'] = array('Contractor'=>'Contractor');

        /**
         * track required fields with this array, Validator uses it and form helper
         * uses it to determine which fields to decorate as 'required' in the UI
         */
        $required_fields = array(
			'contract_type', 
			'contractor_category', 
			'first_name',
			'last_name',
			
            'address_country',
			//'address_province',
			'address_city',
			'address_street',
			'address_postal_code',
			
			'address_billing_country',
			//'address_billing_province',
			'address_billing_city',
			'address_billing_street',
			'address_billing_postal_code',
			
			'phone_number', 
			'email_address', 
			'start_date', 
			'end_date',
            'pay_rate', 
			'payment_limit', 
			'manager',
			'mail_needed',
			'statement_of_work'
		);
        /**
         * This should contain every field in the form (required or not)
         */
        $form = array(
            'employee_type' => 'Contractor',
            'contract_type' => '',
            'contractor_category' => '',
            'first_name' => '',
            'last_name' => '',
            'org_name' => '',
            'address' => '',

			'address_country' => 'United States',
			'address_province' => '',
			'address_city' => '',
			'address_street' => '',
			'address_postal_code' => '',
			
			'address_billing_country' => 'United States',
			'address_billing_province' => '',
			'address_billing_city' => '',
			'address_billing_street' => '',
			'address_billing_postal_code' => '',
			
			'currency' => '',
            'phone_number' => '',
            'email_address' => '',
            'start_date' => '',
            'end_date' => '',
            'pay_rate'  => '',
			'payment_schedule'  => '',
            'payment_limit'  => '',
			'hours_per_week' => '',
            'manager' => '',
            'buddy' => '',
            'location_other' => '',
            'statement_of_work' => '',
            'mail_needed' => '',
            'default_username' => '',
            'mail_alias' => '',
            'mail_lists' => '',
            'other_comments' => '',
                
        );
        $errors = $form;

        if ($_POST) {
            if (!Form::valid_token()) {
            	$this->request->redirect('hiring/contractor');
            }

            Form_Helper::filter_disallowed_values($this->select_lists);
            $post = new Validate($_POST);
            // hack to have Validate keep psot key/values after ->check()
            // that did not have validation rules set (this is only needed
            // since we are not using models
            $post->labels(array_combine(array_keys($form), array_keys($form)));
            $post->filter(true, 'trim');
            $post
                ->rule('start_date', 'date')
                ->rule('end_date', 'date')
                ->rule('email_address', 'email');
            
/*            if (Arr::get($_POST, 'mail_needed')=='1') {
                array_push($required_fields, 'location');
            }
            if (Arr::get($_POST, 'location')=='other') {
                array_push($required_fields,'location_other');
            }
*/
            // add all the required fields
            foreach ($required_fields as $required_field) {
                $post->rule($required_field, 'not_empty');
            }

            if ($post->check()) {
                // check for invalid
                $form = arr::overwrite($form, $post->as_array());
                $form = $this->build_supplemental_form_values($form, $hiring);
                $bugs_to_file = array('Newhire_Contractor');
                if ($form['mail_needed']) {
                    $bugs_to_file[] = 'Newhire_Email';
                }
                // File the appropriate Bugs
                if ($bug_ids = $this->file_these($bugs_to_file, $form)) {
                    // Send Buddy Email
                    /*if ( ! empty($form['buddy']) ) {
                      $this->notify_buddy($form, $hiring);  
                    }*/
                    /**
                     * TODO: Comment this back in when dependency is supported in Bugzilla API
                     */
                    /* try {                          
                        $dependency_result = Filing::set_bug_dependency($bug_ids[0], $bug_ids[1], $this->bugzilla_client);
                    } catch (Exception $e) {
                        Client::messageSend('Could not set dependency for the bug '.$bug_ids[0].': '.$e->getMessage(), E_USER_WARNING);
                    }
                    */
                    $this->request->redirect('hiring/contractor');
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
        // the UI used client to determine which fields to decorate as 'required'
        form::required_fields($required_fields);
        $this->template->js_extra = HTML::script('media/js/jquery.autocomplete.min.js');

        $this->template->title = 'WebTools::Contractor New Hire';
        $this->template->content = new View('pages/hiring/contractor');
        $this->template->content->form = $form;
        $this->template->content->lists = $this->select_lists;
    }
    /**
     * Build needed additional fields for bugzilla submission
     * 
     * @param array $form The validated from input
     * @param Manager_Model $hiring
     * @return $form array with additional values
     */
    private function build_supplemental_form_values(array $form, Model_Hiring $hiring) {
        $first_name = iconv('UTF-8', 'ASCII//TRANSLIT', $form['first_name']);
        $first_initial = iconv('UTF-8', 'ASCII//TRANSLIT', $first_name{0});
        $last_name = iconv('UTF-8', 'ASCII//TRANSLIT', $form['last_name']);
        // build the display user name components
        $additions['fullname']=$first_name . " " . $last_name;
        $additions['username']=strtolower($first_initial.$last_name);
        // build the display manager name parts
        $manager_attributes = $hiring->employee_attributes($form['manager']);
        $additions['manager_bz_email']=isset($manager_attributes['bugzilla_email'])?$manager_attributes['bugzilla_email']:null;
        $additions['manager_name']=isset($manager_attributes['cn'])?$manager_attributes['cn']:null;
        // build the buddy name display parts (if buddy was submitted)
        $additions['buddy_name'] = '';
        if (!empty($form['buddy'])) {
            $buddy_attributes = $hiring->employee_attributes($form['buddy']);
            $additions['buddy_name'] = isset($buddy_attributes['cn'])?$buddy_attributes['cn']:null;
        }
        // merge the addtions w/ the current submitted form elements
        return array_merge($form,$additions);
       
    }
    /**
     * Sends the email to notify the current emp that they are a buddy to this new
     * employee.
     * 
     * @param array $form_input
     * @param Model_Hiring $hiring
     */
    private function notify_buddy($form_input, Model_Hiring $hiring) {
		$template = kohana::config('workermgmt.buddy_email_template');
		$email_info['from_address'] = kohana::config('workermgmt.buddy_email_from_address');
		// make sure label is at least an empty string
		$email_info['from_label'] = kohana::config('workermgmt.buddy_email_from_label')
		  ? kohana::config('workermgmt.buddy_email_from_label')
		  : '';
		$email_info['subject'] = kohana::config('workermgmt.buddy_email_subject');

		$email_info['buddy_name'] = isset($form_input['buddy_name'])?ucwords($form_input['buddy_name']):null;
		$email_info['buddy_email'] = isset($form_input['buddy'])?$form_input['buddy']:null;
		$email_info['newhire_name'] = isset($form_input['fullname'])?ucwords($form_input['fullname']):null;
		$email_info['newhire_email'] = isset($form_input['email_address'])?$form_input['email_address']:null;
		$email_info['hiring_manager_name'] = isset($form_input['manager_name'])?$form_input['manager_name']:null;
		$email_info['hiring_manager_email'] = isset($form_input['manager'])?$form_input['manager']:null;
		if ( ! in_array(null, $email_info, true)) {
		    if ($hiring->notify_buddy($template, $email_info)) {
		      client::messageSend("The Buddy Notification Email was sent to {$email_info['buddy_email']}", E_USER_NOTICE);
		    } else {
		      client::messageSend("The Buddy Notification Email was not sent due to and error", E_USER_ERROR);
		    }
		} else {
		    client::messageSend("The Buddy Notification Email was not sent due to and error", E_USER_ERROR);
		    Kohana_Log::add('error', "Requiered fields missing for \$email_info\n".print_r($email_info,true));
		}
      
    }
	/**
     * Sends the email to notify the people involved in hiring process of a newhire
     * 
	 * 	For employee and seasonal: <recruiting@mozilla.com> and <jcook@mozilla.com> and <[hiring manager]>
	 *	For interns: 			   <jcook@mozilla.com> and <[hiring manager]>
	 *	All 3 of them CC to: 	   <peoplepeople@mozilla.com> and <[office contact]>
	 *
     * @param array $form_input
     * @param Model_Hiring $hiring
     */
    private function send_newhire_notification($aForm, $aBugNames=array(), $aBugs=array()) {
		$aTo = array();
		$aCc = array(
			'peoplepeople@mozilla.com'
		);
		
		if (isset($aForm['manager']) && $aForm['manager']) {
			$aTo[] = $aForm['manager'];
		}
		if (isset($aForm['office_contact']) && $aForm['office_contact']) {
			$aCc[] = $aForm['office_contact'];
		}
		
		switch ($aForm['employee_type']) {
			case 'Intern':
				$aTo[] = 'jcook@mozilla.com';
				break;
			case 'Seasonal':
			case 'Employee':
			default:
				$aTo[] = 'recruiting@mozilla.com';
				$aTo[] = 'jcook@mozilla.com';
		}
		
		$sImmigration = $aForm['immigration'] ? 'Yes' : 'No';
		$sRelocation  = $aForm['relocation']  ? 'Yes' : 'No';
		
		$sBugs = '';
		for ($n=0; $n<count($aBugs); $n++) {
			$sBugs .= 'Bug #'.$aBugs[$n].' - '.(ucwords(str_replace('_', ' ', $aBugNames[$n])))."\n";
		}
		
		$sCc = implode(',', $aCc);
		$sTo = implode(',', $aTo);
		$sSubject = 'FYI: Signed Offer Letter Received from '.$aForm['first_name'].' '.$aForm['last_name'];
		$sHeaders = 'Cc: ' . $sCc . "\r\n";
		$sHeaders .= 'From: peoplepeople@mozilla.com' . "\r\n";
		$sBody = <<<TEXT
Hello,

Sending notification that {$aForm['first_name']} {$aForm['last_name']}'s signed offer letter and new hire paperwork have been received.

A new hire bug for has been created for your employee. Bug #'s are listed below:

$sBugs

Start Date:     {$aForm['start_date']}
End Date:       {$aForm['end_date']}
Position:       {$aForm['position_title']}
Department:     {$aForm['department']}
Reporting to:   {$aForm['manager_name']}
Location:       {$aForm['end_date']}
Personal Email: {$aForm['email_address']}
Immigration?    $sImmigration
Relocating?     $sRelocation
Hire Type:      {$aForm['hire_type']}
Referral?       -
TEXT;

		mail($sTo, $sSubject, $sBody, $sHeaders);

    }
}
