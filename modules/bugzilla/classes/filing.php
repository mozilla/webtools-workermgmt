<?php defined('SYSPATH') or die('No direct script access.');

class Filing {

    /*
     * product (string) Required - The name of the product the bug is being
     *   filed against.
     *
     * component (string) Required - The name of a component in the product above.
     *
     * summary (string) Required - A brief description of the bug being filed.
     *
     * version (string) Required - A version of the product above;
     *   the version the bug was found in.
     *
     * description (string) Defaulted - The initial description for this bug.
     *   Some Bugzilla installations require this to not be blank.
     *
     * op_sys (string) Defaulted - The operating system the bug was discovered on.
     *
     * platform (string) Defaulted - What type of hardware the bug was experienced on.
     *
     * priority (string) Defaulted - What order the bug will be fixed in by
     *   the developer, compared to the developer's other bugs.
     *
     * severity (string) Defaulted - How severe the bug is.
     *
     * alias (string) - A brief alias for the bug that can be used instead of
     *   a bug number when accessing this bug. Must be unique in all of this Bugzilla.
     *
     * assigned_to (username) - A user to assign this bug to, if you don't
     *   want it to be assigned to the component owner.
     *
     * cc (array) - An array of usernames to CC on this bug.
     *
     * groups (array) - An array of group names to put this bug into. You can
     *   see valid group names on the Permissions tab of the Preferences
     *   screen, or, if you are an administrator, in the Groups control panel.
     *   Note that invalid group names or groups that the bug can't be restricted
     *   to are silently ignored. If you don't specify this argument, then a
     *   bug will be added into all the groups that are set as being "Default"
     *   for this product. (If you want to avoid that, you should specify groups
     *   as an empty array.)
     *
     * qa_contact (username) - If this installation has QA Contacts enabled,
     *   you can set the QA Contact here if you don't want to use the component's
     *   default QA Contact.
     *
     * status (string) - The status that this bug should start out as. Note
     *   that only certain statuses can be set on bug creation.
     *
     * target_milestone (string) - A valid target milestone for this product.
     *
     */

    protected $_filters = array(
        true => array('trim' => array()),
    );
    /**
     * The allowed attributes list is built from this list, so it is
     * REQUIRED that there is an entry for every field you would want filed.
     * If a field is optional or you don't care what field you send, just set
     *
     *   'optional_field' => null,
     *
     * this also supports arrays as attributes
     *
     *   'optional_field' => 'array',
     *
     *   usage:
     *     $filing->foo = 'value1';
     *     $filing->foo = 'value2';
     *   The value for $filing->foo will be:
     *     array(0 => value1, 1 => value2)
     * 
     */
    protected $field_definitions = array(
        /*
         * REQUIRED fields (noting what the bzilla docs state as required
         *   but not enforcing here.  We let Bugzilla do that and deal with the
         *   returned error)
         */
        'product'       => null,
        'component'     => null,
        'summary'       => null,
        'version'       => null,
        'description'   => null,
        /*
         * These will go to default values, probably best practice
         * to explicitly set them
         */
        "op_sys"    => null,
        "platform"  => null,
        "priority"  => null,
        "severity"  => null,
        /*
         * Optional
         */
        "alias"             => null,
        "assigned_to"       => null,
        "cc"                => 'array',
        "groups"            => 'array',
        "qa_contact"        => null,
        "status"            => null,
        "target_milestone"  => null
    );

    /*
     * The attributes for this model, they are designed to match one to one
     * with the attributes available to the bugzilla
     * xmlrpc call 'Bugzilla.create'
     */
    protected $attributes = array();
    /*
     * Typically this is an array of data that came from a submitted
     * html form. It is used to populate $this->attributes
     */
    protected $submitted_data = array();

    protected $required_submitted_data = array();


    /**
	 * Creates and returns a new model.
	 *
	 * @chainable
	 * @param   string  model name
	 * @param   mixed   parameter for find()
	 * @return  ORM
	 */
	public static function factory($model, $id = NULL) {
		// Set class name
		$model = 'Filing_'.ucfirst($model);

		return new $model($id);
	}
    /**
     *
     * @param array $submitted_data
     */
    public function __construct(array $submitted_data) {
        $this->attributes = array_fill_keys(array_keys($this->field_definitions), null) ;
        $this->submitted_data = $submitted_data;
        $this->check_required_input();
    }
    
    public function __get($name) {
        return key_exists($name, $this->attributes) ? $this->attributes[$name] : null;
    }
    /*
     * supports integer indexed attributes
     *
     * You signify an attribute in an array attrib by
     * $this->field_definitions['foo'] => 'array';
     */
    public function __set($name, $value) {
        if(key_exists($name, $this->attributes)) {
            // check if this is an array attribute
            if($this->field_definitions[$name]=='array' &&  ! is_array($value)) {
                $this->attributes[$name][] = $value;
            } else {
                $this->attributes[$name] = $value;
            }
        }
    }
    /**
     * conatinates the current $this->$name with param $value
     * 
     * @param string $name
     * @param string $value
     * @param boolean $add_newline If to prefix $value with "\n"
     */
    public function append_to($name, $value, $add_newline=true) {
        if(key_exists($name, $this->attributes) && $this->field_definitions[$name]!='array') {
            $value = $add_newline ? "\n{$value}" : $value;
            $this->attributes[$name] .= $value;
        }
    }

    public function has_required_submitted_data() {
        $unsupplied_fields = array_diff_key($this->required_submitted_data, $this->submitted_data);
        if($unsupplied_fields) {
            print_r($unsupplied_fields);die;
        }
        
    }





    
    public function __toString() {
        return "Model_Filing\n".print_r($this->attributes, true);
    }

}