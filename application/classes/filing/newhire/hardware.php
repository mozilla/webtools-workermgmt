<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 */
class Filing_Newhire_Hardware extends Filing {
    
    protected $label = "Hardware request";

    /**
     * @see Filing::construct_content()
     * @see config/workermgmt.php $config['bug_defaults']
     */
    public function  construct_content() {
        parent::construct_content();
        $t = $this;

        $this->summary = "Hardware Request - {$t->input('fullname')} ({$t->input('start_date')})";
        $this->description = "Name: {$t->input('fullname')}\n"
            . "Username: {$t->input('username')}\n"
            . "Type: {$t->input('hire_type')}\n"
            . "Manager: {$t->input('manager_name')}\n"
            . "Start date: {$t->input('start_date')}";

        if($t->input('hire_type')=='Intern') {
            $this->append_to('description', "End of Internship: {$t->input('end_date')}");
        }

        $location = $t->input('location') == "other" 
            ? $t->input('location_other')
            : $t->input('location');
        $this->append_to('description',
            "Location: {$location}\n"
            ."Machine: {$t->input('machine_type')}"
        );
        $special_request = $t->input('machine_special_requests');
        if( ! empty($special_request)) {
            $this->append_to('description',
                "Special Requests: {$special_request}"
            );
        }
        $this->cc = $t->input('manager_bz_email');
        $this->groups = array(self::CODE_EMPLOYEE_HIRING_GROUP);
      
    }
    
}
