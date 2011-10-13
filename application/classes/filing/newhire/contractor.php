<?php defined('SYSPATH') or die('No direct script access.');

class Filing_Newhire_Contractor extends Filing {

    protected $label = "Human Resources notification";
    
    /**
     * @see Filing::construct_content()
     * @see config/workermgmt.php $config['bug_defaults']
     */
    public function  construct_content() {
        parent::construct_content();
        $t = $this;

		$fullname = $t->input('first_name').' '.$t->input('last_name');
        $summary_2nd_half = ($t->input('org_name') 
			? $t->input('org_name') 
			: $fullname
		);
        $this->summary = "Contractor Request - {$summary_2nd_half} ({$t->input('start_date')})";

        $org_string = $t->input('org_name') !== null ? "Organization Name: {$t->input('org_name')}":"";
        $this->description = $org_string;

        $org_name = $t->input('org_name');
        $contact_string = ! empty($org_name)
            ? "Contact: $fullname\n"
            : "Name: $fullname\n";
        $this->append_to('description',
            $contact_string
            . "Work Address: " . 
				$t->input('address_street') . ', ' .
				$t->input('address_city') . ' ' .
				$t->input('address_province') . ' ' .
				$t->input('address_postal_code') . ', ' .
				$t->input('address_country') ."\n"
			. "Billing Address: " . 
				$t->input('address_billing_street') . ', ' .
				$t->input('address_billing_city') . ' ' .
				$t->input('address_billing_province') . ' ' .
				$t->input('address_billing_postal_code') . ', ' .
				$t->input('address_billing_country') ."\n"
            . "Phone: " . $t->input('phone_number') . "\n"
            . "E-mail: " . $t->input('email_address') . "\n"
            . "Start of contract: " . $t->input('start_date') . "\n"
            . "End of contract: " . $t->input('end_date') . "\n"
            . "Rate of pay: " . 
				$t->input('pay_rate') . ' ' .
				$t->input('currency') . "\n"
			. "Payment schedule: " . $t->input('payment_schedule') . "\n"
            . "Total payment limitation: " . 
				$t->input('payment_limit') . ' ' .
				$t->input('currency') . "\n"
			. "Hours per week: {$t->input('hours_per_week')}\n"
            . "Manager: {$t->input('manager_name')}\n"
            . "Type: {$t->input('contract_type')}\n"
            . "Category: {$t->input('contractor_category')}\n"
            . "Statement of work:\n{$t->input('statement_of_work')}\n"
        );
        $this->cc = array(
			$t->input('manager_bz_email'),
			'eanda@mozilla.com'
		);
        $this->groups = array(self::CODE_CONTRACTOR_HIRING_GROUP);
    }
    
}
