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
        
        $this->product = "Websites";
        $this->component = "Other";
        $this->summary = "New Web Dev Project: {$t->input('name')}";
        $this->description = "Overview: {$t->input('overview')}";
        $this->append_to('description', "\nProject Scope: {$t->input('scope')}");
        $this->append_to('description', "\nDependencies: {$t->dependencies_text()}");
        $this->append_to('description', "\nAssumptions: {$t->input('assumptions')}");
        $this->append_to('description', "\nDeliverables: {$t->input('deliverables')}");

        // add cc's
//        $this->cc = "accounting@mozilla.com";
//        $this->cc = $t->input('manager_bz_email');
        $this->groups = array(self::CODE_EMPLOYEE_HIRING_GROUP);
        
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
    
}
