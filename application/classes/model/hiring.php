<?php defined('SYSPATH') or die('No direct script access.');

class Model_Hiring {
  /**
   * Class constructor.
   *
   * @access	public
   * @return	void
   */
  public function __construct(Ldap $ldap) {
    $this->ldap = $ldap;
    $this->log = Kohana_Log::instance();
  }

  public function manager_list($use_bugzilla_email = false) {
      return $this->ldap->employee_list('manager', $use_bugzilla_email);
  }
  public function all_emps_list($use_bugzilla_email = false) {
      return $this->ldap->employee_list('all', $use_bugzilla_email);
  }
  public function employee_attributes($ldap_email) {
      return $this->ldap->employee_attributes($ldap_email);
  }
  /**
   *
   * Available $template vars are:
   *  {{buddy_name}}
   *  {{newhire_name}}
   *  {{newhire_email}}
   *  {{hiring_manager_name}}
   *  {{hiring_manager_email}}
   *
   * These keynames will be in $email_info
   *
   * @param string $email_template
   * @param string $from_emai
   * @param array $email_info
   * ex: array(
   *  [from_address] => noreplay@somewhere.com
   *  [from_label] => Buddy Notifier
   *  [subject] => Your Buddy Email
   *  [buddy_name] => My Buddy
   *  [buddy_email] => mybuddy@somewhere.com
   *  [newhire_name] => New Guy
   *  [newhire_email] => newguy@yahoo.com
   *  [hiring_manager_name] => Joe Manager
   *  [hiring_manager_email] => jmanager@somewhere.com
   * )
   */
  public function notify_buddy($email_template, $email_info) {
    $mail_sent = false;
    if(kohana::config('workermgmt.send_email')) {
      // filter what will be the from and to emails for the mail() function
      if(filter_var($email_info['from_address'], FILTER_VALIDATE_EMAIL)
         && filter_var($email_info['buddy_email'], FILTER_VALIDATE_EMAIL)) {

        // replace elements in email template and subject line
        foreach ($email_info as $placeholder_key => $value) {
          $email_template = str_replace("%{$placeholder_key}%", $value, $email_template);
          $email_info['subject'] = str_replace("%{$placeholder_key}%", $value, $email_info['subject']);
        }
         $from = !empty ($email_info['from_label']) ? "\"{$email_info['from_label']}\" <{$email_info['from_address']}>" : $email_info['from_address'];
         $mail_sent = mail($email_info['buddy_email'], $email_info['subject'], $email_template, "From: ". $from);
         $this->log->add('info', "Sent Buddy Notification Email to [{$email_info['buddy_email']}] from [{$from}]");
      } else {
        $this->log->add('error', "One or both To and From email addresses for the Notify Buddy email were invalid\n"
                ."To: {$email_info['buddy_email']}");
      }
    } else {
      $this->log->add('debug', "config('workermgmt.send_email') is false so Buddy notification email not sent");
    }
    return $mail_sent;
  }

  
  

}