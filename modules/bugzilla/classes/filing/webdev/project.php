<?php defined('SYSPATH') or die('No direct script access.');

class Filing_Webdev_Project extends Filing {
    
    protected $required_input_fields = array(
//        'fullname',
//        'start_date',
//        'username',
//        'hire_type',
//        'manager_name',
//        'manager',
//        'buddy_name',
//        'buddy',
//        'end_date',
//        'location',
//        'location_other',
//        'manager_bz_email'
    );
    /**
     * label that will be used for messaging.
     */
    protected $label = "Web Dev Project";
    
    public function  contruct_content() {
        $t = $this;
        
        $this->product = "Core";
        $this->component = "Tracking";
        $this->version = 'unspecified';
        $this->summary = "{$t->input('name')}";
        $this->description = "Overview: {$t->input('overview')}";
        $this->append_to('description', "\nProject Scope: {$t->input('scope')}");
        $this->append_to('description', "\nDependencies: {$t->dependencies_text()}");
        $this->append_to('description', "\nAssumptions: {$t->input('assumptions')}");
        $this->append_to('description', "\nDeliverables: {$t->input('deliverables')}");

        // add cc's
        $this->cc = $t->build_carbon_copies(array("morgamic@gmail.com"));
//        $this->cc = $t->input('manager_bz_email');
        $this->groups = array(self::CODE_EMPLOYEE_HIRING_GROUP);
        $this->assigned_to = 'malexis@mozilla.com';
        
    }

    private function dependencies_text() {
        $dependencies_text = null;
        foreach ($this->submitted_data as $key => $value) {
            if(substr($key, 0, 13)=='dependencies_' && ! empty ($value) ) {
                $dependencies_text .= ("\n\t". ucfirst(substr($key, 13)).": {$value}");
            }
        }
        return $dependencies_text ? $dependencies_text : "None";
    }
    private function build_carbon_copies(array $default_ccs = array()) {
        $carbon_copies = $default_ccs;
        foreach ($this->submitted_data as $key => $value) {
            if(substr($key, 0, 8)=='members_' &&  ! empty($value) ) {
                $value = is_array($value)?$value:array($value);
                // check that value is not empty and that is does not
                // already exist in the list of cc's
                foreach ($value as $cc) {
                    if( !empty ($cc) && ! in_array($cc, $carbon_copies)) {
                        $carbon_copies[] = $cc;
                    }
                }
            }
        }
        return $carbon_copies;
    }
    
}
