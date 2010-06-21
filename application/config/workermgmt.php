<?php defined('SYSPATH') OR die('No direct access allowed.');

$config['bugzilla_url'] = 'https://bugzilla-stage-tip.mozilla.org';
$config['ldap_anon_bind'] = 'uid=binduser,ou=logins,dc=mozilla';
$config['ldap_anon_password'] = 'Z6bp3Zrh';
$config['ldap_host'] = 'pm-ns.mozilla.org';
$config['ldap_base_dn'] = 'o=com,dc=mozilla';
$config['use_mock_ldap'] = false;

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
 *   $config['email_from_address'] = 'somebody@somewhere.com'
 *   $config['email_from_label'] = 'Somebody';
 */
$config['buddy_email_from_address'] = 'noreply@mozilla.com';
$config['buddy_email_from_label'] = 'Buddy Notifier';
$config['buddy_email_subject'] = 'This is the Buddy Notifier';

return $config;