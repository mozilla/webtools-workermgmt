<?php defined('SYSPATH') or die('No direct script access.');

class Filing_Newhire_Email extends Filing {
    
    protected $label = "Mail account request";

    /**
     * @see Filing::construct_content()
     * @see config/workermgmt.php $config['bug_defaults']
     */
    public function  construct_content() {
        parent::construct_content();
        $t = $this;

		$sEndDate = $t->input('end_date') ? $t->input('end_date') : 'Indefinite';
		$sEmailNeeded = $t->input('mail_needed') ? 'Yes' : 'No';
		$sMachineNeeded = $t->input('machine_needed') ? $t->input('machine_type') : 'None';
		
        $this->summary = "LDAP/Zimbra Account Request - {$t->input('fullname')} ".
			"<{$t->input('username')}@mozilla.com>".
			" ({$t->input('start_date')} - $sEndDate)";

        $this->description = "Name: {$t->input('fullname')}\n"
            . "Type: {$t->input('employee_type')}\n"
			. "Personal email: {$t->input('email_address')}\n"
            . "Mozilla email: {$t->input('username')}@mozilla.com\n"
			. "Work address: {$t->input('address_street')}, {$t->input('address_city')} {$t->input('address_province')} {$t->input('address_postal_code')}, {$t->input('address_country')}\n"
            . "Manager: {$t->input('manager_name')} ({$t->input('manager')})\n"
            . "Buddy: {$t->input('buddy_name')} ({$t->input('buddy')})\n"
			. "Office contact: {$t->input('office_contact')}\n"
            . "Start date: {$t->input('start_date')}\n"
			. "End date: $sEndDate\n"
			. "Mail needed: $sEmailNeeded\n"
			. "Mail alias: {$t->input('mail_alias')}\n"
			. "Mail lists: {$t->input('mail_lists')}\n"
			. "Machine needed: $sMachineNeeded\n"
			. "Machine special requests: {$t->input('machine_special_requests')}\n"
			. "Comments: {$t->input('other_comments')}";
			
   /*     $location = $t->input('location') == "other"?$t->input('location_other'):$t->input('location');
        $this->append_to('description', "Location: {$location}");
        $alias = $t->input('mail_alias');
        if( ! empty ($alias)) {
            $this->append_to('description', "Alias: {$alias}");
        }
        $lists = $t->input('mail_lists');
        if( ! empty ($lists)) {
            $this->append_to('description', "Mailing lists: {$lists}");
        }
        $other = $t->input('other_comments');
        if(!empty ($other)) {
            $this->append_to('description', "Other comments: {$other}");
        }
*/
        $this->cc = array(
			$t->input('manager_bz_email')
		);
        $this->groups = array(self::CODE_EMPLOYEE_HIRING_GROUP);
    }
}
