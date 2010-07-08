<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Bugzilla library.
 *
 * @see http://www.bugzilla.org/docs/tip/en/html/api/Bugzilla/WebService/Bug.html
 *
 *
 * @package    Bugzilla
 * @author     skeen@mozilla.org
 */
class Bugzilla_Client {
    private $error_message;
    private $curler;

    private $bz_id=null;
    private $bz_token=null;

    private $config;

    private $log;

    public function  __construct(Kohana_Config_File $config) {
        $this->config = $config;
        $this->bz_id = Session::instance()->get('bz_id');
        $this->bz_token = Session::instance()->get('bz_token');
        $this->curler = new Curler();
        $this->log = Kohana_Log::instance();
    }
    /**
     * After a failed interaction w/ Bugzilla, this will contain the error message
     * that was returned.
     * @return string
     */
    public function error_message() {
        return $this->error_message;
    }
    /**
     * Accessor for config values.
     * 
     * @param string
     */
    public function config($key) {
        return isset ($this->config[$key]) ? $this->config[$key] : null;

    }

    /**
     * Return a static instance of Bugzilla.
     *
     * @return  object
     */
    public static function instance(Kohana_Config_File $config) {
        static $instance;
        empty($instance) and $instance = new self($config);
        return $instance;
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
            $this->log->add('error',"Recieved Empty response from bugzilla login request");
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
    public function call($xml) {
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
                'ssl_verify_peer' => ! kohana::config('workermgmt.in_dev_mode')
                )
        );
        $set_cookies = $this->curler->response_headers('Set-Cookie');
        $response = $this->curler->response_content();

        $this->log->add('debug',"Curl Info from XMLRPC call in [".__METHOD__
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
        $this->log->add('debug',"Response from XMLRPC call in [".__METHOD__
                ."] with \$response = ".print_r($response,1));
        return $response;
    }

}