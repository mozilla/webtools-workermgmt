<?php defined('SYSPATH') or die('No direct script access.');

class Filing_Newhire_Setup extends Filing {
    
    /**
     * label that will be used for messaging.
     */
    protected $label = "Karen/Accounting notification";

    /**
     * @see Filing::construct_content()
     * @see config/workermgmt.php $config['bug_defaults']
     */
    public function  construct_content() {
        parent::construct_content();
        $t = $this;
        
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
        
        /**
         * Adding Hilary Hall for all Toronto new hires
         * See bug #590667
         */
        if ($location == 'Toronto') {
            $this->cc = array(
                $t->input('manager_bz_email'),
                'hhall@mozilla.com'
            );
        } else {
            $this->cc = $t->input('manager_bz_email');
        }
        $this->groups = array(self::CODE_EMPLOYEE_HIRING_GROUP);
    }
    
}
