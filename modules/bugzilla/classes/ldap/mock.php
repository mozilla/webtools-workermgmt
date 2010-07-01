<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author     skeen@mozilla.org
 * @license    http://www.opensource.org/licenses/mit-license.php
 */
class Ldap_Mock extends Ldap {

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
    Kohana_Log::instance()->add('debug',"**USING MockLdap**");
    $this->manager_list = $config['mock_ldap_manager_list']
            ? $config['mock_ldap_manager_list']
            : $this->manager_list;
    return parent::__construct($config, $credentials);
  }

  public function employee_list($type='all') {
    Kohana_Log::instance()->add('debug',"Called MOCK ".__METHOD__);
    return json_decode($this->manager_list, true);
  }
  public function employee_attributes($ldap_email) {
    Kohana_Log::instance()->add('debug',"Called MOCK ".__METHOD__);
    $manager_list = json_decode($this->manager_list, true);
    return isset($manager_list[$ldap_email])
      ? $manager_list[$ldap_email]
      : array();
  }
}