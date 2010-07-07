<?php defined('SYSPATH') or die('No direct script access.');

class Filing_Newhire_Email extends Filing {
    
    protected $required_input_fields = array(
        'fullname',
        'start_date',
        'end_date',
        'username',
        'hire_type',
        'manager_name',
        'manager',
        'location',
        'location_other',
        'mail_alias',
        'mail_lists',
        'other_comments',
        'manager_bz_email'
    );
    /**
     * sprintf expecting bugzilla_url, bug_id that was just created
     * @see Filing->success_message()
     */
    protected $success_message = 'Mail account request -- <a href="%s/show_bug.cgi?id=%d" target="_blank">bug %d</a>';

    public function  contruct_content() {
        $t = $this;
        
        $this->product = "mozilla.org";
        $this->component = "Server Operations: Account Requests";
        $this->summary = "LDAP/Zimbra Account Request - {$t->input('fullname')} "
            ."<{$t->input('username')}@mozilla.com> ({$t->input('start_date')})";
        $this->description =
            "Name: {$t->input('fullname')}\n" .
            "Username: {$t->input('username')}\n" .
            "Type: " . $t->input('hire_type') . "\n" .
            "Manager: {$t->input('manager_name')}\n" .
            "Start date: {$t->input('start_date')}";

        if($t->input('hire_type')=='Intern') {
            $this->append_to('description', "End of Internship: {$t->input('end_date')}");
        }
        $location = $t->input('location') == "other"?$t->input('location_other'):$t->input('location');
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
        $this->cc = $t->input('manager_bz_email');
        $this->groups = array(self::CODE_EMPLOYEE_HIRING_GROUP);
      
    }
    
}
