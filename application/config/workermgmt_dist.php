<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * if in_dev_mode is true
 *  - Allowes the use of $config['use_mock_ldap'] = true;
 *  - Turns off ssl verification for curl calls to bugzilla
 */
$config['in_dev_mode'] = false;
// if true, will send email to buddy specified in hiring form
$config['send_email'] = true;

$config['bugzilla_url'] = 'https://bugzilla.mozilla.org';
$config['ldap_anon_bind'] = '';
$config['ldap_anon_password'] = '';
$config['ldap_host'] = '';
$config['ldap_base_dn'] = 'o=com,dc=mozilla';
$config['use_mock_ldap'] = false;

$config['mock_ldap_manager_list'] = "";

// Buddy email config settings
$config['buddy_email_template'] = <<<XOXO
Greetings %buddy_name%!

You are receiving this email because you have been assigned to be a Buddy to %newhire_name%, one of our new hires or interns.

You can get more information about what it means to be a Buddy, and what's expected of you, on the Mozilla intranet, here:
 
https://intranet.mozilla.org/Buddy_System
 
You can get started by contacting %newhire_name% at %newhire_email%!  Please try to reach out and make first contact with your Buddy in the next few days -- do what you can to build their excitement about their new job and let them know that they have someone to talk to if they have any initial questions.

If you have any questions about this, you should contact %hiring_manager_name%, the hiring manager.  If you need any help with figuring out what you should do next, you can contact Deb Richardson (deb@mozilla.com), or someone in the Recruiting team of the People department.
XOXO;

/**
 * For the desired from address: "Somebody" <somebody@somewhere.com>
 *  $config['email_from_address'] = 'somebody@somewhere.com'
 *  $config['email_from_label'] = 'Somebody';
 */

$config['buddy_email_from_address'] = 'noreply@mozilla.com';
$config['buddy_email_from_label'] = 'Buddy Notifier';
$config['buddy_email_subject'] = 'This is the Buddy Notifier'; 
 
$config['bug_defaults'] = array(
    // all bugs start with these defaults
    '_ALL_' => array(
        'version'   => 'other',
        'platform'  => 'All',
        'op_sys'    => 'other',
        'severity'  => 'normal',
    ),
    // default for specific bugs
    'Newhire_Contractor' => array(
        'product' => 'Mozilla Corporation',
        'component' => 'Consulting',
        'cc' => array('accounting@mozilla.com'),
    ),
    'Webdev_Project' => array(
        'product' => 'mozilla.org',
        'component' => 'Webdev',
        'version' => 'other',
        'cc' => array('morgamic@gmail.com'),
        'assigned_to' => 'malexis@mozilla.com'
     ),
);

return $config;
