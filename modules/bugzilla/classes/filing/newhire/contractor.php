<?php defined('SYSPATH') or die('No direct script access.');

class Filing_Newhire_Contractor extends Filing {
    
    protected $required_input_fields = array(
        'org_name',
        'address',
        'phone_number',
        'email_address',
        'fullname',
        'start_date',
        'end_date',
        'pay_rate',
        'payment_limit',
        'contract_type',
        'contractor_category',
        'statement_of_work',
        'manager_name',
        'manager',
        'location',
        'location_other',
        'manager_bz_email'
    );
    /**
     * sprintf expecting bugzilla_url, bug_id that was just created
     * @see Filing->success_message()
     */
    protected $success_message = 'Human Resources notification -- <a href="%s/show_bug.cgi?id=%d" target="_blank">bug %d</a>';

    public function  contruct_content() {
        $t = $this;
        
        $this->product = "Mozilla Corporation";
        $this->component = "Consulting";

        $summary_2nd_half = ($t->input('org_name')!==null ? $t->input('org_name') : $t->input('fullname'));
        $this->summary = "Contractor Request - {$summary_2nd_half} ({$t->input('start_date')})";

        $org_string = $t->input('org_name')!==null ? "Organization Name: {$t->input('org_name')}":"";
        $this->description = $org_string;

        $contact_string = !empty($t->input('org_name'))
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
        $this->cc = "accounting@mozilla.com";
        $this->cc = $t->input('manager_bz_email');
        $this->groups = array(self::CODE_CONTRACTOR_HIRING_GROUP);
        
    }
    
}
