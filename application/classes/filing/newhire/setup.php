<?php defined('SYSPATH') or die('No direct script access.');

class Filing_Newhire_Setup extends Filing {
    
    /**
     * label that will be used for messaging.
     */
    protected $label = "Newhire notification";

    /**
     * @see Filing::construct_content()
     * @see config/workermgmt.php $config['bug_defaults']
     */
    public function  construct_content() {
        parent::construct_content();
        $t = $this;
        
		$sEndDate 		= $t->input('end_date') 		? $t->input('end_date') : 'Indefinite';
		$sEmailNeeded 	= $t->input('mail_needed') 		? 'Yes' : 'No';
		$sMachineNeeded = $t->input('machine_needed') 	? $t->input('machine_type') : 'None';
		$sRelocation 	= $t->input('relocation') 		? 'Yes' : 'No';
		$sImmigration 	= $t->input('immigration') 		? 'Yes' : 'No';
		
        $this->summary = "New Hire Notification - {$t->input('fullname')} ({$t->input('start_date')} - $sEndDate)";

        $this->description = 
			  "Name:              {$t->input('fullname')}\n"
            . "Employee type:     {$t->input('employee_type')}\n"
			. "Hire type:         {$t->input('hire_type')}\n"
			. "Title:             {$t->input('position_title')}\n"
			. "Department:        {$t->input('department')}\n"
			. "Personal email:    {$t->input('email_address')}\n"
            . "Mozilla email:     {$t->input('username')}@mozilla.com\n"
			. "Work address:      {$t->input('address_street')}, {$t->input('address_city')} {$t->input('address_province')} {$t->input('address_postal_code')}, {$t->input('address_country')}\n"
            . "Manager:           {$t->input('manager_name')} ({$t->input('manager')})\n"
            . "Buddy:             {$t->input('buddy_name')} ({$t->input('buddy')})\n"
			. "Office contact:    {$t->input('office_contact')}\n"
            . "Start date:        {$t->input('start_date')}\n"
			. "End date:          $sEndDate\n"
			. "Relocation:        $sRelocation\n"
			. "Immigration:       $sImmigration\n"
			. "Mail needed:       $sEmailNeeded\n"
			. "Mail alias:        {$t->input('mail_alias')}\n"
			. "Mail lists:        {$t->input('mail_lists')}\n"
			. "Machine needed:    $sMachineNeeded\n"
			. "Machine special requests: {$t->input('machine_special_requests')}\n"
			. "Comments:          {$t->input('other_comments')}";
			
		switch ($t->input('employee_type')) {
			case 'Intern':
				$this->cc = array(
		        	$t->input('manager_bz_email')
		       	);
				break;
			case 'Seasonal':
			case 'Employee':
			default:
				$this->cc = array(
		        	$t->input('manager_bz_email'),
					'emcclure@mozilla.com',
					'dcoleman@mozilla.com',
					'jill@mozilla.com',
					'lgray@mozilla.com',
					'rberto@mozilla.com'
		       	);
				break;
		}

        $this->groups = array(self::CODE_EMPLOYEE_HIRING_GROUP);
    }
}
