<?php defined('SYSPATH') or die('No direct script access.');

class Filing_Webdev_Project extends Filing {
    
    protected $required_input_fields = array(
    );
    /**
     * label that will be used for messaging.
     */
    protected $label = "Web Dev Project";

    /**
     * @see Filing::contruct_content()
     * @see config/workermgmt.php $config['bug_defaults']
     */
    public function  contruct_content() {
        parent::contruct_content();
        $t = $this;
        
        $this->summary = "{$t->input('name')}";
        $this->description = "Overview: {$t->input('overview')}";
        $this->append_to('description', "\nProject Scope: {$t->input('scope')}");
        $this->append_to('description', "\nDependencies: {$t->dependencies_text()}");
        $this->append_to('description', "\nAssumptions: {$t->input('assumptions')}");
        $this->append_to('description', "\nDeliverables: {$t->input('deliverables')}");
        // pull together the cc's from Team Members section of the form
        $t->add_carbon_copies();
        $this->groups = array(self::CODE_EMPLOYEE_HIRING_GROUP);

        
    }
    /**
     * Concat (with a smidge of formatting) the various dependency sections
     * of the form
     * 
     * @return string
     */
    private function dependencies_text() {
        $dependencies_text = null;
        foreach ($this->submitted_data as $key => $value) {
            if(substr($key, 0, 13)=='dependencies_' && ! empty ($value) ) {
                $dependencies_text .= ("\n\t". ucfirst(substr($key, 13)).": {$value}");
            }
        }
        return $dependencies_text ? $dependencies_text : "None";
    }
    /**
     * Add cc's by collecting them from the Team Members section
     * of the form
     * 
     * return void
     */
    private function add_carbon_copies() {
        foreach ($this->submitted_data as $key => $value) {
            if(substr($key, 0, 8)=='members_' &&  ! empty($value) ) {
                $value = is_array($value) ? $value : array($value);
                // check that value is not empty and that is does not
                // already exist in the list of cc's
                foreach ($value as $cc) {
                    if( !empty ($cc) && ! in_array($cc, $this->cc)) {
                        $this->cc = $cc;
                    }
                }
            }
        }
    }
    
}
