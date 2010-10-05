<?php defined('SYSPATH') or die('No direct script access.');

class Filing_Leaving_Setup extends Filing {
    
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
        /*
        'full_name'             => '',
        'reason_for_leaving'    => '',
        'manager'               => '',
        'date_of_notice'        => '',
        'date_of_last_day'      => '',
        'equipment_to_return'   => '',
        'do_notify_managers'    => ''
        */
        $this->summary = "{$t->input('full_name')} is leaving company on {$t->input('date_of_last_day')}";
        $this->description = "Name: {$t->input('full_name')}\n"
            . "Manager: ({$t->input('manager')})\n"
            . "Reason for leaving: ({$t->input('reason_for_leaving')})\n"
            . "Notice received: ({$t->input('date_of_notice')})\n"
            . "Last day of work: ({$t->input('date_of_last_day')})\n"
            . "Equipment to return: {$t->input('equipment_to_return')}";
        $this->cc = $t->input('manager');
        $this->groups = array(self::CODE_EMPLOYEE_HIRING_GROUP);
    }
    
}
