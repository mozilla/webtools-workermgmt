<?php defined('SYSPATH') or die('No direct script access.');
/**
 *
 * Requires XMLRPC ext @see http://php.net/manual/en/book.xmlrpc.php
 */
abstract class Filing {

    const ERROR_CODE_LOGIN_REQUIRED = 410;

    const CODE_EMPLOYEE_HIRING_GROUP = 'mozilla-corporation-confidential'; //26;
    const CODE_CONTRACTOR_HIRING_GROUP = 'consulting'; //59;

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
        "cc"                => null,
        "groups"            => null,
        "qa_contact"        => null,
        "status"            => null,
        "target_milestone"  => null
    );
    // these are defined as 'array' attributes (see $this->__get()
    private $array_attributes = array('cc', 'groups');

    /**
     * made available through __get
     */
    protected $bug_id;

    /**
     * Set this to a human approriate label for this bug in the child class.
     * Used for messaging
     *
     * Reasonable default set in __contructor
     *
     * made available through __get
     */
    protected $label = null;

    /**
     * Success Message
     * Allowed insertable params are
     *   - {label} : Replaced with $this->label
     *   - {bug} : replaced with hyperlink to the bug that was created
     *         <a target="_blank" href="https://bugzilla-stage-tip.mozilla.org/show_bug.cgi?id=577133">bug 577133</a>
     *
     * made available through __get
     */
    protected $success_message = "{label} -- {bug} created";
    
    /**
     * Typically this is an array of data that came from a submitted
     * html form. It is used to populate $this->attributes
     */
    protected $submitted_data = array();
    
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
        $oNewClass = new $filing_class($submitted_data, $bz_connector);
		return $oNewClass;
	}
    /**
     *
     * @param array $submitted_data
     */
    protected function __construct(array $submitted_data, $bz_connector) {
        $this->attributes = array_fill_keys(array_keys($this->attributes), null);
        $this->bz_connector = $bz_connector;
        $this->submitted_data = $submitted_data;
        if($this->label===null) {
            $this->label = str_replace(array('Filing_','_'), array('',' '), get_class($this));
        }
    }

    public function __get($key) {
        if(key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
        // allow public gtting of these attributes
        if(in_array($key, array('bug_id', 'label', 'success_message', 'submitted_data','attributes'))) {
            return $this->$key;
        }
        return null;
    }
    /*
     * supports integer indexed attributes
     *
     * You signify an attribute in an array attrib by
     * adding it to $this->array_attributes;
     */
    public function __set($key, $value) {
        if(key_exists($key, $this->attributes)) {
            // check if this is an array attribute
            if(in_array($key, $this->array_attributes) &&  ! is_array($value)) {
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
     * This is where the business logic of how to contruct the bug goes,
     * so children of this classes to supply any values needed for the
     * specific bug.
     *
     * @see config/workermgmt.php $config['bug_defaults'] on how to
     * construct defualt bug values.
     *
     * be sure to call parent::construct_content(); first in your
     * child class's construct_content method
     */
    public function construct_content() {
        $filing_name = strtolower(str_replace('Filing_', '', get_class($this)));
        if($bugs_defaults = Kohana::config("workermgmt.bug_defaults")) {
            $bugs_defaults = array_change_key_case($bugs_defaults);
            // add default that apply to ALL filings
            if(key_exists('_all_', $bugs_defaults) && $bugs_defaults['_all_']) {
                foreach ($bugs_defaults['_all_'] as $attribute_key => $value) {
                    $this->$attribute_key = $value;
                }
            }
            // add defaults specific to this type of filing
            if(key_exists($filing_name, $bugs_defaults) && $bugs_defaults[$filing_name]) {
                foreach ($bugs_defaults[$filing_name] as $attribute_key => $value) {
                    $this->$attribute_key = $value;
                }
            }
        }
    }

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
         * Take the user input and construct the content for the bug filing
         */
        $this->construct_content();
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
                'product'       => $this->product       ? $this->product    : 'Mozilla Corporation',
                'component'     => $this->component     ? $this->component  : 'Facilities Management',
                'summary'       => $this->summary,
                'groups'        => $this->groups,
                'description'   => $this->description,
                'cc'            => $this->cc,
                'version'       => $this->version       ? $this->version    : 'other',
                'platform'      => $this->platform      ? $this->platform   : 'All',
                'op_sys'        => $this->op_sys        ? $this->op_sys     : 'All',
                'severity'      => $this->severity      ? $this->severity   : 'minor',
                'assigned_to'   => $this->assigned_to,
            ),
            array(
                'escaping' => array('markup'),
                'encoding' => 'utf-8'
            )
        );
        return xmlrpc_decode($this->bz_connector->call($request));
    }
    /**
     * Sets bugs dependency
     * @param Int     $dependent 
     * @param Int     $depends_on 
     * @param Object  $bugzilla_connector 
     */
    public static function set_bug_dependency($dependent, $depends_on, $bugzilla_client) {
        $request = xmlrpc_encode_request(
            "Bug.update",
            array(
                'ids'           => array($dependent),
                'depends_on'    => array('add' => array($depends_on))
            ),
            array(
                'escaping' => array('markup'),
                'encoding' => 'utf-8'
            )
        );
        return xmlrpc_decode($bugzilla_client->call($request));
    }

    /**
     * used by file() to build the content of the bug
     *
     * @throws EXCEPTION_MISSING_INPUT
     * @param string $key key in $this->submitted_data
     * @return string
     */
    protected function input($key) {
        if( !key_exists ($key, $this->submitted_data)) {
            return '';
        }
        return $this->submitted_data[$key];
    }

    
    public function __toString() {
        return "Model_Filing\n".print_r($this->attributes, true);
    }

}