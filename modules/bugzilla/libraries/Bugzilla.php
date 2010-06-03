<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Bugzilla library.
 *
 *
 * @package    Bugzilla
 * @author     skeen@mozilla.org
 * @license    http://www.opensource.org/licenses/mit-license.php
 */
class Bugzilla_Core {
    private $error_message;
    private $curler;


    private $bz_id=null;
    private $bz_token=null;

    private $config;

    /**
     * Bug types we know about, these correspond to the case:'s
     * in $this::newhire_filing()
     */
    const BUG_HR_CONTRACTOR = 'hr_contractor';
    const BUG_EMAIL_SETUP = 'email_setup';
    const BUG_HARDWARE_REQUEST = 'hardware_request';
    const BUG_NEWHIRE_SETUP = 'newhire_setup';

    const CODE_LOGIN_REQUIRED = 410;
    const CODE_EMPLOYEE_HIRING_GROUP = 26;
    const CODE_CONTRACTOR_HIRING_GROUP = 59;


    public function  __construct($config) {
        $this->config = $config;
        $this->bz_id = Session::instance()->get('bz_id');
        $this->bz_token = Session::instance()->get('bz_token');
        $this->curler = new Curler();
    }

    public function error_message() {
        return $this->error_message;
    }

    /**
     * Return a static instance of Bugzilla.
     *
     * @return  object
     */
    public static function instance(array $config) {
        static $instance;
        empty($instance) and $instance = new self($config);
        return $instance;
    }

    /**
     * The types of bugs we can file for.  To add more,  add a CONST above and
     * an array element here.  Then add a case: in $this::newhire_filing()
     */
    private $bug_filing_types = array(
        self::BUG_HR_CONTRACTOR => array(
            "success_message" => 'Human Resources notification -- <a href="%s/show_bug.cgi?id=%d" target="_blank">bug %d</a>'
        ),
        self::BUG_NEWHIRE_SETUP => array(
            "success_message" => 'Karen/Accounting notification -- <a href="%s/show_bug.cgi?id=%d" target="_blank">bug %d</a>'
        ),
        self::BUG_EMAIL_SETUP => array(
            "success_message" => 'Mail account request -- <a href="%s/show_bug.cgi?id=%d" target="_blank">bug %d</a>'
        ),
        self::BUG_HARDWARE_REQUEST => array(
            "success_message" => 'Hardware request -- <a href="%s/show_bug.cgi?id=%d" target="_blank">bug %d</a>'
        )
    );

    /**
     *
     * @todo This still needs A LOT of work.  Need a much more elegant templated
     *  solution (samkeen)
     *
     * @param array $input
     * @return array $result
     * array(
     *  'error_code' => null,
     *  'error_message' => null,
     *  'bug_id' => null,
     *  'success_message' => null
     * );
     */
    public function newhire_filing($request_type, array $input) {
        $result = array(
            'error_code' => null,
            'error_message' => null,
            'bug_id' => null,
            'success_message' => null
        );
        $filing_response = array();
        
        switch ($request_type) {
            
            case self::BUG_EMAIL_SETUP:
                $new_hiring_info['product'] = "mozilla.org";
                $new_hiring_info['component'] = "Server Operations: Account Requests";
                $new_hiring_info['summary'] = "LDAP/Zimbra Account Request - {$input['fullname']} <{$input['username']}@mozilla.com> ("
                        . $input['start_date'] . ")";
                $new_hiring_info['description'] =
                    "Name: {$input['fullname']}\n" .
                    "Username: {$input['username']}\n" .
                    "Type: " . $input['hire_type'] . "\n" .
                    "Manager: {$input['manager_name']}\n" .
                    "Start date: " . $input['start_date'] . "\n";
                if($input['hire_type']=='Intern') {
                    $new_hiring_info['description'] .= "End of Internship: {$input['end_date']}\n";
                }
                $location = $input['location'] == "other"?$input['location_other']:$input['location'];
                $new_hiring_info['description'] .= "\nLocation: {$location}";
                if(!empty ($input['mail_alias'])) {
                    $new_hiring_info['description'] .= "\nAlias: {$input['mail_alias']}";
                }
                if(!empty ($input['mail_lists'])) {
                    $new_hiring_info['description'] .= "\nMailing lists: {$input['mail_lists']}";
                }
                if(!empty ($input['other_comments'])) {
                    $new_hiring_info['description'] .= "\nOther comments: {$input['other_comments']}";
                }
                $new_hiring_info['ccs'][] = $input['bz_manager'];
                $new_hiring_info['groups'] = array(self::CODE_EMPLOYEE_HIRING_GROUP);

                $filing_response = $this->file_bug($new_hiring_info);
                break;

            case self::BUG_HARDWARE_REQUEST:
                $new_hiring_info['product'] = "mozilla.org";
                $new_hiring_info['component'] = "Server Operations: Desktop Issues";
                $new_hiring_info['summary'] = "Hardware Request - {$input['fullname']} ({$input['start_date']})";
                $new_hiring_info['description'] = "Name: {$input['fullname']}\n"
                    . "Username: {$input['username']}\n"
                    . "Type: {$input['hire_type']}\n"
                    . "Manager: {$input['manager_name']}\n"
                    . "Start date: {$input['start_date']}\n";
                if($input['hire_type']=='Intern') {
                    $new_hiring_info['description'] .= "End of Internship: {$input['end_date']}\n";
                }
                $location = $input['location'] == "other"?$input['location_other']:$input['location'];
                $new_hiring_info['description'] .= "\nLocation: {$location}\n"
                    ."Machine: " . $input['machine_type'] . "\n";
                if(!empty ($input['machine_special_requests'])) {
                    $new_hiring_info['description'] .= "\nSpecial Requests: {$input['machine_special_requests']}";
                }
                
                $new_hiring_info['ccs'][] = $input['bz_manager'];
                $new_hiring_info['groups'] = array(self::CODE_EMPLOYEE_HIRING_GROUP);

                $filing_response = $this->file_bug($new_hiring_info);
                break;

            case self::BUG_NEWHIRE_SETUP:

                $new_hiring_info['product'] = "Mozilla Corporation";
                $new_hiring_info['component'] = "Facilities Management";
                $new_hiring_info['summary'] = "New Hire Notification - {$input['fullname']} ({$input['start_date']})";
                $new_hiring_info['description'] = "Name: {$input['fullname']}\n"
                    . "E-mail: {$input['username']}@mozilla.com\n"
                    . "Type: {$input['hire_type']}\n"
                    . "Manager: {$input['manager_name']} ({$input['manager']})\n"
                    . "Buddy: {$input['buddy_name']} ({$input['buddy']})\n"
                    . "Start date: {$input['start_date']}\n";
                if($input['hire_type']=='Intern') {
                    $new_hiring_info['description'] .= "End of Internship: {$input['end_date']}\n";
                }
                $location = $input['location'] == "other"?$input['location_other']:$input['location'];
                $new_hiring_info['description'] .= "\nLocation: {$location}\n";
                
                $new_hiring_info['ccs'][] = "accounting@mozilla.com";
                $new_hiring_info['ccs'][] = $input['bz_manager'];
                $new_hiring_info['groups'] = array(self::CODE_EMPLOYEE_HIRING_GROUP);

                $filing_response = $this->file_bug($new_hiring_info);

                break;

            case self::BUG_HR_CONTRACTOR:
                $new_hiring_info['product'] = "Mozilla Corporation";
                $new_hiring_info['component'] = "Consulting";
                $summary_2nd_half = ($input['org_name']!==null ? $input['org_name'] : $input['fullname']);
                $new_hiring_info['summary'] = "Contractor Request - {$summary_2nd_half} ({$input['start_date']})";
                $org_string = $input['org_name']!==null ? "Organization Name: {$input['org_name']}\n":"";
                $new_hiring_info['description'] = $org_string;
                $contact_string = !empty($input['org_name'])
                    ? "Contact: {$input['fullname']}\n"
                    : "Name: {$input['fullname']}\n";
                $new_hiring_info['description'] .= $contact_string
                    . "Address: " . $input['address'] . "\n"
                    . "Phone: " . $input['phone_number'] . "\n"
                    . "E-mail: " . $input['email_address'] . "\n"
                    . "Start of contract: " . $input['start_date'] . "\n"
                    . "End of contract: " . $input['end_date'] . "\n"
                    . "Rate of pay: " . $input['pay_rate'] . "\n"
                    . "Total payment limitation: " . $input['payment_limit'] . "\n"
                    . "Manager: {$input['manager_name']}\n";
                $location = $input['location'] == "other"?$input['location_other']:$input['location'];
                $new_hiring_info['description'] .= "\nLocation: {$location}\n"
                    . "Type: " . $input['contract_type'] . "\n"
                    . "Category: " . $input['contractor_category'] . "\n\n"
                    . "Statement of work:\n" . $input['statement_of_work'] . "\n";

                $new_hiring_info['ccs'][] = "accounting@mozilla.com";
                $new_hiring_info['ccs'][] = $input['bz_manager'];
                $new_hiring_info['groups'] = array(self::CODE_CONTRACTOR_HIRING_GROUP);

                $filing_response = $this->file_bug($new_hiring_info);
                break;

            default:
                Kohana::log('error',"Urecognized Filing reqest type [$request_type]", E_USER_ERROR);
                break;
        }
        kohana::log('debug', "\$filing_response:".print_r($filing_response,1));
        $error_code = isset($filing_response['faultCode'])?$filing_response['faultCode']:null;
        $error_message = isset($filing_response['faultString'])?$filing_response['faultString']:null;
        if($error_message) {
            $result['error_code']=$error_code;
            $result['error_message']=$error_message;
        }
        /**
         * if we get error code CODE_LOGIN_REQUIRED (login required), no need to try the
         * rest of these, just redrect to login.php
         */
        if($result['error_code']&&$result['error_code']==self::CODE_LOGIN_REQUIRED) {
            client::messageSend($result['error_message'], E_USER_ERROR);
            url::redirect('login');
        }
        if(isset($filing_response['id'])) {
            $result['bug_id'] = isset($filing_response['id'])?$filing_response['id']:null;
            $result['success_message'] = sprintf(
                    $this->bug_filing_types[$request_type]['success_message'],
                    $this->config['bugzilla_url'], $result['bug_id'], $result['bug_id']
            );
        }
        return $result;
    }

    /**
     * Atempt to authenticate to the Bugzilla system
     * If an error is returned it is set to $this->error_message
     *
     * @param string $username
     * @param string $password
     * @return boolean
     */
    public function login($username, $password) {
        $login_success = false;
        $request = xmlrpc_encode_request(
                "User.login",
                array(
                'login'     => $username,
                'password'  => $password
                )
        );
        $response = xmlrpc_decode_request($this->call($request), $request);
        if(empty($response)) {
            $this->error_message = "There was an unexpected error while logging into Bugzilla";
            kohana::log('error',"Recieved Empty response from bugzilla login request");
        } else if (isset($response['faultString'])) {
            $this->error_message = $response['faultString'];
        } else {
            Session::instance()->set('bz_id', $this->bz_id);
            Session::instance()->set('bz_token', $this->bz_token);
            $login_success = true;
        }

        return $login_success;
    }
    public function logout() {
        Session::instance()->delete('bz_id', 'bz_token');
    }
    public function authenticated() {
        return $this->bz_id && $this->bz_token;
    }
    /**
     *
     * @param sting $xml
     * @return
     */
    private function call($xml) {
        $bugzilla_server = $this->config['bugzilla_url'];
        $bugzilla_xmlrpc = "/xmlrpc.cgi";
        $additional_headers = array('Content-type: text/xml;charset=UTF-8');
        if ( $this->bz_id && $this->bz_token ) {
            $cookie = "Cookie: Bugzilla_login="
                    ."{$this->bz_id}; Bugzilla_logincookie={$this->bz_token}";
            ("Setting Cookie Header: [$cookie]");
            $additional_headers[] = $cookie;
        }
        $this->curler->post($bugzilla_server.$bugzilla_xmlrpc, $xml,
                array(
                'return_headers'=>true,
                'headers' => $additional_headers,
                // when not in production have curl ignore ssl warings which are
                // most ofter due to self signed certs
                'ssl_verify_peer' => ! IN_DEV_MODE
                )
        );
        $set_cookies = $this->curler->response_headers('Set-Cookie');
        $response = $this->curler->response_content();

        kohana::log('debug',"Curl Info from XMLRPC call in [".__METHOD__
                ."] \n".print_r($this->curler->response_info(),1));

        if($set_cookies) {
            $bz_creds = array();
            foreach ($set_cookies as $set_cookie) {
                $match = null;
                preg_match('/(Bugzilla_login|Bugzilla_logincookie)?=(.*?);/i', $set_cookie, $match);
                if(isset($match[2])) {
                    if(strtolower($match[1])=='bugzilla_login') {
                        $this->bz_id = $match[2];
                    } else if(strtolower($match[1])=='bugzilla_logincookie') {
                        $this->bz_token = $match[2];
                    }
                }
            }
        }
        kohana::log('debug',"Response from XMLRPC call in [".__METHOD__
                ."] with \$response = ".print_r($response,1));
        return $response;
    }

    /**
     * File a bug in the Bugzilla system
     *
     * @param $bug_meta The data needed to build the bug request
     *  array(
     *   'product'=>null,
     *   'component'=>null,
     *   'summary'=>null,
     *   'description'=>null,
     *   'ccs'=>null,
     *   'groups'=>null,
     *  )
     */
    private function file_bug($bug_meta) {
        // ensure these keys
        $bug_meta = array_merge(
            array(
                'product'=>null,
                'component'=>null,
                'summary'=>null,
                'groups'=>null,
                'description'=>null,
                'ccs'=>null,
            ),
            array_change_key_case($bug_meta)
        );

        kohana::log('debug',"Starting [".__METHOD__."] with \$bug_meta = "
                .print_r($bug_meta,1));

        $request = xmlrpc_encode_request(
            "Bug.create",
            array(
                'product' => $bug_meta['product'],
                'component' => $bug_meta['component'],
                'summary' => $bug_meta['summary'],
                'groups' => $bug_meta['groups'],
                'description' => $bug_meta['description'],
                'cc' => $bug_meta['ccs'],

                'version' => 'other',
                'platform' => 'All',
                'op_sys' => 'Other',
                'severity' => 'normal',
            ),
            array(
                'escaping' => array('markup'),
                'encoding' => 'utf-8'
            )
        );
        $method = null;
        $response = xmlrpc_decode_request($this->call($request),$method);
        return xmlrpc_decode_request($this->call($request), $request);
    }

}