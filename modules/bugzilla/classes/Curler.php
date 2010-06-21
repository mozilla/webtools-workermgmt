<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Curler library.
 *
 *
 * @package    Curler
 * @author     skeen@mozilla.org
 * @license    http://www.opensource.org/licenses/mit-license.php
 */
class Curler_Core {
  
  private $cookie_file =null;
  private $response_info = null;
  private $config = null;
  private $response_headers = null;
  private $response_content = null;

  public function __construct() {
    
  }
  /**
   *
   * @param string $url
   * @param mixed $post_fields Array or 'get' style string
   * @param array $config
   * @return string The full HTTP Response
   */
  public function post($url, $post_fields, array $config=array()) {
    $this->config = array_merge(
      array(
        'headers' => array(),
        'return_headers' => false, // include headers in return
        'authenticate' => null, //to auth, use: array('username'=>...,'password'=>...)
        'cookies' => false,
        'ssl_verify_peer' => true,//turn off for dev if going against selfsigned certs
      ),
      $config
    );

    $authenticate = is_array($this->config['authenticate'])
      ? array_merge(array('username'=>null,'password'=>null),$this->config['authenticate'])
      : null;
    $handle = curl_init(); // initialize curl handle
    // if given an array break it apart and encode the pieces
    // if it is a string, use as is.
    $post_fields_string = is_array($post_fields)
      ? $this->request_fields_string($post_fields)
      : $post_fields;

    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, $this->config['ssl_verify_peer']);
    curl_setopt($handle, CURLOPT_HTTPHEADER, $this->config['headers']);
    curl_setopt($handle, CURLOPT_HEADER, (bool)$this->config['return_headers']);

    if ($this->config['cookies']) curl_setopt($handle, CURLOPT_COOKIEFILE, $this->cookie_file);
    if ($this->config['cookies']) curl_setopt($handle, CURLOPT_COOKIEJAR, $this->cookie_file);

    curl_setopt($handle, CURLOPT_URL, $url); // set url to post to
    curl_setopt($handle, CURLOPT_FAILONERROR, 1);
    curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
    curl_setopt($handle, CURLOPT_RETURNTRANSFER,1); // return into a variable
    curl_setopt($handle, CURLOPT_TIMEOUT, 10); // times out after 4s
    curl_setopt($handle, CURLOPT_POST, true); // set POST method
    curl_setopt($handle, CURLOPT_POSTFIELDS, $post_fields_string); // add POST fields
    if ($authenticate) {
      if (!empty($authenticate['username'])&&!empty($authenticate['password'])) {
        curl_setopt($handle,CURLOPT_USERPWD,$authenticate['username'] . ":" . $authenticate['password']);
      } else {
        curl_close($handle);
        throw new Exception("Authentication Credentials Not set".__METHOD__);
      }
    }
    $http_response = curl_exec($handle); // run the whole process
    if($http_response===false) {
      kohana::log('error',"Curl Error Number [".curl_errno($handle)."] : ".curl_error($handle));
    }
    kohana::log('debug',"Return from curl_exec:\n".print_r($http_response,1));
    $this->response_info = curl_getinfo($handle);
    $this->parse_response($http_response);
    curl_close($handle);
    return $http_response;
  }


  /**
   * Get a certain (or all) response headers
   * @param sting $header_name Name of the response header (i.e. Set-Cookie)
   *  if null given, all headers are returned.
   *  single headers are always returned as an integer indexed array
   * 
   * @return array
   */
  public function response_headers($header_name=null) {
    $headers = null;
    if(is_array($this->response_headers)) {
      $header_name = strtolower($header_name);
      if($header_name===null) {
        $headers = $this->response_headers;
      } else {
        $headers = key_exists($header_name, $this->response_headers)
          ? $this->response_headers[$header_name]
          : null;
      }
    }
    return $headers;
  }
  /**
   *
   * @return string The content of the http response (minus any headers)
   */
  public function response_content() {
    return $this->response_content;
  }
  /**
   *
   * @return array The Curl formed inof array (curl_getinfo())
   */
  public function response_info() {
    return $this->response_info;
  }



  private function get_request_fields_string($request_fields, $encode=true) {
    $url_params = $this->request_fields_string($request_fields, $encode);
    return $url_params ? "?{$url_params}" : '';
  }
  private function request_fields_string($request_fields, $encode=true) {
    $post_fields_string = null;
    if(!empty($request_fields)) {
      // if we are given a string, break it apart so we can perform encoding on it
      if ( ! is_array($request_fields)) {
        $request_fields = ''.trim($request_fields,'?&');
        $request_fields = explode('=',$request_fields);
      }
      // encode the keys and values
      if($encode) {
        foreach ($request_fields as $key => $value) {
          $post_fields_string[] = urlencode($key).'='.urlencode($value);
        }
      } else {
        foreach ($request_fields as $key => $value) {
          $post_fields_string[] = "{$key}={$value}";
        }
      }
      // link the key=val pairs with & to complete the string.
      $post_fields_string = implode('&',$post_fields_string);
    }
    return $post_fields_string;
  }
  /**
   *
   * @param string $http_response Disect the http response,  split out any
   * headers from content.
   */
  private function parse_response($http_response) {
    if($this->config['return_headers']) {
      $processed_headers = null;
      $response_parts = preg_split('/\r?\n\r?\n/', $http_response);
      // find the dividing index between header and content
      $i=0;
      while (preg_match('/^HTTP[\.\/\d\s]+\d+/',$response_parts[$i],$match)) {
        $i++;
      }
      $headers = implode("\n",array_slice($response_parts,0,$i));
      $headers = preg_split('/[\r?\n]+/', $headers);
      $this->response_content = implode("\n\n",array_slice($response_parts,$i));
      foreach ($headers as $header) {
        $header = explode(':', $header,2);
        if(isset ($header[1])) {
          $processed_headers[trim($header[0])][]=trim($header[1]);
        }
      }
      $this->response_headers = is_array($processed_headers)
        ? array_change_key_case($processed_headers, CASE_LOWER)
        : $processed_headers;
    } else {
      $this->response_content = $result;
    }
  }
}