<?php defined('SYSPATH') OR die('No direct access allowed.');

$config['bugzilla_url'] = 'BUGZILLA DEV or PROD URL';
$config['ldap_anon_bind'] = '';
$config['ldap_anon_password'] = '';
$config['ldap_host'] = '';
$config['ldap_base_dn'] = '';
/**
 * use_mock_ldap
 * The app in PRODUCTION grabs the ldap username and passwd from the
 * LDAP backed HTTPAuth.
 * If you are testing and not behind this sort of setup, use MockLdap
 * to proxy the 2 LDAP calls and return test data.
 *
 *  - manager_list() Returns the list of manager to populatet the manager
 *    select list in the hiring forms
 *  - manager_attributes() At this point only used in hiring forms to get the
 *    cn and bugzilla email for a given managers email
 *
 * @see lib/MockLdap
 * The app will NOT allow 'use_mock_ldap' to be turned on for IN_PRODUCTION
 */
$config['use_mock_ldap'] = false;

/**
 * For the desired from address: "Somebody" <somebody@somewhere.com>
 *   $config['email_from_address'] = 'somebody@somewhere.com'
 *   $config['email_from_label'] = 'Somebody';
 */
$config['buddy_email_from_address'] = 'Someone';
$config['buddy_email_from_label'] = 'someone@somewhere.com';
$config['buddy_email_subject'] = 'Hello';

$config['buddy_email_template'] = <<<XOXO
This is the email Body template with place holders in this form

Dear %email_recipient%,

THis email is to inform you that...
XOXO;
