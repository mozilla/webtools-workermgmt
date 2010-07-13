<?php defined('SYSPATH') or die('No direct script access.');
/**
 *
 * Requires XMLRPC ext @see http://php.net/manual/en/book.xmlrpc.php
 */
abstract class Filing {

    const ERROR_CODE_LOGIN_REQUIRED = 410;

    const CODE_EMPLOYEE_HIRING_GROUP = 26;
    const CODE_CONTRACTOR_HIRING_GROUP = 59;

    const EXCEPTION_MISSING_INPUT = 100;
    const EXCEPTION_BUGZILLA_INTERACTION = 200;
    const EXCEPTION_AUTHENTICATION_FAILED = 300;

    /**
     * @see http://www.bugzilla.org/docs/tip/en/html/api/Bugzilla/WebService/Bug.html
     * 
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

    /**
     * The attributes for this model, they are designed to match one to one
     * with the attributes available to the bugzilla
     * xmlrpc call 'Bugzilla.create'
     *
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
    protected $attributes = array(
        /*
         * These 5 are the REQUIRED fields (noting what the bzilla docs state as
         *   required but not enforcing here.  We let Bugzilla do that and
         *   deal with the returned error)
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
        // these are defined as 'array' attributes (see $this->__get()
        "cc"                => 'array',
        "groups"            => 'array',
        "qa_contact"        => null,
        "status"            => null,
        "target_milestone"  => null
    );

    /**
     * made available through __get
     */
    protected $bug_id;

    /**
     * Set this to a human approriate label for this bug in the child class.
     * Used for messaging
     *
     * made available through __get
     */
    protected $label = "Unspecified";
    
    /**
     * Typically this is an array of data that came from a submitted
     * html form. It is used to populate $this->attributes
     */
    protected $submitted_data = array();

    /**
     * this is an array of the keys required to be present in
     * $submitted_data.  They can have empty values, it's just
     * required that the keys are there.
     */
    protected $required_input_fields = array();
    
    /**
     * array of any field that were found to be missing
     */
    protected $missing_required_fields = array();

    private $bz_connector;

    /**
	 * Creates and returns a new model.
	 *
	 * @chainable
	 * @param   string  model name
	 * @param   mixed   parameter for find()
	 * @return  Filing
	 */
	public static function factory($filing_class, $submitted_data, $bz_connector) {
		// Set class name
		$filing_class = 'Filing_'.ucfirst($filing_class);
        return new $filing_class($submitted_data, $bz_connector);
	}
    /**
     *
     * @param array $submitted_data
     */
    protected function __construct(array $submitted_data, $bz_connector) {
        $this->attributes = array_fill_keys(array_keys($this->attributes), null);
        $this->bz_connector = $bz_connector;
        $this->submitted_data = $submitted_data;
        $this->missing_required_fields = $this->required_input_fields;
        $this->set_defaults();
    }
    /**
     * Some common sensible defaults
     * You can overide these in the
     */
    protected function set_defaults() {
        $this->version = 'other';
        $this->platform = 'All';
        $this->op_sys = 'Other';
        $this->severity = 'normal';
    }

    public function __get($key) {
        if(key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
        // allow public gtting of these attributes
        if(in_array($key, array('bug_id', 'label', 'submitted_data','attributes','required_input_fields'))) {
            return $this->$key;
        }
        return null;
    }
    /*
     * supports integer indexed attributes
     *
     * You signify an attribute in an array attrib by
     * $this->field_definitions['foo'] => 'array';
     */
    public function __set($key, $value) {
        if(key_exists($key, $this->attributes)) {
            // check if this is an array attribute
            if($this->attributes[$key]=='array' &&  ! is_array($value)) {
                $this->attributes[$key][] = $value;
            } else {
                $this->attributes[$key] = $value;
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
        if(key_exists($name, $this->attributes) && $this->attributes[$name]!='array') {
            $value = $add_newline ? "\n{$value}" : $value;
            $this->attributes[$name] .= $value;
        }
    }
    /**
     * Check that all the required input filed keys are present in the
     * given input.
     * This signals that we are ready to attempt to file the bug
     *
     * @throws self::EXCEPTION_MISSING_INPUT
     * 
     * @return void
     */
    public function validate_required_input_fields() {
        $this->missing_required_fields = array_diff(
            $this->required_input_fields,
            array_keys($this->submitted_data)
        );
        if($this->missing_required_fields) {
            $msg = "Missing required fields: "
                . implode(', ', $this->missing_required_fields);
            throw new Exception($msg, self::EXCEPTION_MISSING_INPUT);
            Kohana_Log::instance()->add('error', __METHOD__." $msg");
            
        }
    }
    /**
     * This is where the business logic of how to contruct the bug goes,
     * so children of this class are required to implement it.
     */
    public abstract function contruct_content();

    /**
     *
     * @throws EXCEPTION_MISSING_INPUT
     * @throws EXCEPTION_BUGZILLA_INTERACTION
     *
     * @return void
     */
    public function file() {
        $filing_response = array();
        /**
         * verify that all required key are present in the supplied user input
         */
        $this->validate_required_input_fields();
        /**
         * Take the user input an contruct the content for the bug filing
         */
        $this->contruct_content();
        /**
         * send the bug to Bugzilla.
         */
        $filing_response = $this->send_bug_request();
        Kohana_Log::instance()->add('debug', __METHOD__." \$filing_response:".print_r($filing_response,1));
        if($filing_response==null) {
            Kohana_Log::instance()->add('error', __METHOD__." null returned from \$this->send_bug_request()");
            throw new Exception("null returned from the request to Bugzilla", self::EXCEPTION_BUGZILLA_INTERACTION);
        }
        // look for errors in the response from Bugzilla
        $error_code = isset($filing_response['faultCode'])
            ? $filing_response['faultCode']
            : null;
        $error_message = isset($filing_response['faultString'])
            ? $filing_response['faultString']
            : null;
        /**
         * Auth failed, session probably timed out.
         */
        if($error_code == self::ERROR_CODE_LOGIN_REQUIRED) {
            throw new Exception("Authentication failed, code[{$error_code}]", self::EXCEPTION_AUTHENTICATION_FAILED);
        }
        // for any other errors, contruct and throw an Exception
        if($error_message) {
            throw new Exception("$error_message, code[{$error_code}]", self::EXCEPTION_BUGZILLA_INTERACTION);
        }
        /**
         * grab the Id os the bug created
         */
        $this->bug_id = isset($filing_response['id']) ? $filing_response['id'] : null;
    }
    /**
     * Make the actual xml rpc request to Bugzilla
     * 
     * @return array
     */
    private function send_bug_request() {
        $log = Kohana_Log::instance();

        $log->add('debug',"Starting [".__METHOD__."] with \$bug_meta = {$this}");
        $request = xmlrpc_encode_request(
            "Bug.create",
            array(
                'product' => $this->product,
                'component' => $this->component,
                'summary' => $this->summary,
                'groups' => $this->groups,
                'description' => $this->description,
                'cc' => $this->cc,

                'version' => $this->version,
                'platform' => $this->platform,
                'op_sys' => $this->op_sys,
                'severity' => $this->severity,
            ),
            array(
                'escaping' => array('markup'),
                'encoding' => 'utf-8'
            )
        );
        return xmlrpc_decode($this->bz_connector->call($request));
    }

    /**
     * used by file() to build the content of the bug
     *
     * @throws EXCEPTION_MISSING_INPUT
     * @param string $key key in $this->submitted_data
     * @return string
     */
    protected function input($key) {
        // any key asked for here should always exist.
        if( ! key_exists ($key, $this->submitted_data)) {
            throw new Exception(
                "Asked for non-existent submitted_data key: [{$key}]",
                self::EXCEPTION_MISSING_INPUT);
        }
        return $this->submitted_data[$key];
    }

    
    public function __toString() {
        return "Model_Filing\n".print_r($this->attributes, true);
    }

}