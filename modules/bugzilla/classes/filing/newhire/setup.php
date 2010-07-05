<?php defined('SYSPATH') or die('No direct script access.');

class Filing_Newhire_Setup extends Filing {
    
    protected $required_input_fields = array(
        'fullname',
        'start_date',
        'username',
        'hire_type',
        'manager_name',
        'manager',
        'buddy_name',
        'buddy',
        'end_date',
        'location',
        'location_other',
        'manager_bz_email'
    );

    public function  contruct_content() {
        $t = $this;
        
        $this->product = "Mozilla Corporation";
        $this->component = "Facilities Management";
        $this->summary = "New Hire Notification - {$t->input('fullname')} ({$t->input('start_date')})";
        $this->description = "Name: {$t->input('fullname')}\n"
            . "E-mail: {$t->input('username')}@mozilla.com\n"
            . "Type: {$t->input('hire_type')}\n"
            . "Manager: {$t->input('manager_name')} ({$t->input('manager')})\n"
            . "Buddy: {$t->input('buddy_name')} ({$t->input('buddy')})\n"
            . "Start date: {$t->input('start_date')}";
        if($t->input('hire_type')=='Intern') {
            $this->append_to('description', "End of Internship: {$t->input('end_date')}");
        }
        $location = $t->input('location') == "other" 
            ? $t->input('location_other')
            : $t->input('location');
        $this->append_to('description', "Location: {$location}");
        $this->cc = "accounting@mozilla.com";
        $this->cc = $t->input('manager_bz_email');
        $this->groups = array(self::CODE_EMPLOYEE_HIRING_GROUP);
        
    }
    
}
