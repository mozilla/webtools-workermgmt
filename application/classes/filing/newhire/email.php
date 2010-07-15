<?php defined('SYSPATH') or die('No direct script access.');

class Filing_Newhire_Email extends Filing {
    
    protected $label = "Mail account request";

    /**
     * @see Filing::contruct_content()
     * @see config/workermgmt.php $config['bug_defaults']
     */
    public function  contruct_content() {
        parent::contruct_content();
        $t = $this;

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
