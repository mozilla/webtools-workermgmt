<?php defined('SYSPATH') or die('No direct script access.');

class Filing_Newhire_Contractor extends Filing {

    protected $label = "Human Resources notification";
    
    /**
     * @see Filing::contruct_content()
     * @see config/workermgmt.php $config['bug_defaults']
     */
    public function  contruct_content() {
        parent::contruct_content();
        $t = $this;

        $summary_2nd_half = ($t->input('org_name')!==null ? $t->input('org_name') : $t->input('fullname'));
        $this->summary = "Contractor Request - {$summary_2nd_half} ({$t->input('start_date')})";

        $org_string = $t->input('org_name')!==null ? "Organization Name: {$t->input('org_name')}":"";
        $this->description = $org_string;

        $org_name = $t->input('org_name');
        $contact_string = ! empty($org_name)
            ? "Contact: {$t->input('fullname')}\n"
            : "Name: {$t->input('fullname')}\n";

        $this->append_to('description',
            $contact_string
            . "Address: " . $t->input('address') . "\n"
            . "Phone: " . $t->input('phone_number') . "\n"
            . "E-mail: " . $t->input('email_address') . "\n"
            . "Start of contract: " . $t->input('start_date') . "\n"
            . "End of contract: " . $t->input('end_date') . "\n"
            . "Rate of pay: " . $t->input('pay_rate') . "\n"
            . "Total payment limitation: " . $t->input('payment_limit') . "\n"
            . "Manager: {$t->input('manager_name')}"
        );
        $location = $t->input('location') == "other"
            ? $t->input('location_other')
            : $t->input('location');
        $this->append_to('description',
            "Location: {$location}\n"
            . "Type: {$t->input('contract_type')}\n"
            . "Category: {$t->input('contractor_category')}\n\n"
            . "Statement of work:\n{$t->input('statement_of_work')}\n"
        );
        $this->cc = $t->input('manager_bz_email');
        $this->groups = array(self::CODE_CONTRACTOR_HIRING_GROUP);
        
    }
    
}
