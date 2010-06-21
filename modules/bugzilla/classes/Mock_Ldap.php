<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Mock_Ldap library.
 *
 *
 * @package    Mock_Ldap
 * @author     skeen@mozilla.org
 * @license    http://www.opensource.org/licenses/mit-license.php
 */
class Mock_Ldap_Core extends Ldap_Core {

  private $manager_list =
    '{"bob1@somewhere.com":{
        "cn":"Bob One",
        "title":"Number One Bob",
        "bugzilla_email":"bug-bob1@somewhere.com"
    },
    {"bob2@somewhere.com":{
        "cn":"Bob Two",
        "title":"Number Two Bob",
        "bugzilla_email":"bug-bob2@somewhere.com"
    }';
  

  public function  __construct($config, $credentials) {
    kohana::log('debug',"**USING MockLdap**");
    $this->manager_list = $config['mock_ldap_manager_list'];
    return parent::__construct($config, $credentials);
  }

  public function manager_list() {
    kohana::log('debug',"Called MOCK ".__METHOD__);
    return json_decode($this->manager_list, true);
  }
  public function employee_attributes($ldap_email) {
    kohana::log('debug',"Called MOCK ".__METHOD__);
    $manager_list = json_decode($this->manager_list, true);
    return isset($manager_list[$ldap_email])
      ? $manager_list[$ldap_email]
      : array();
  }
}