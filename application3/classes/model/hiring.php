<?php defined('SYSPATH') or die('No direct script access.');

class Hiring_Model {
  /**
   * Class constructor.
   *
   * @access	public
   * @return	void
   */
  public function __construct(Ldap_Core $ldap) {
    $this->ldap = $ldap;
  }

  public function manager_list() {
      return $this->ldap->employee_list('manager');
  }
  public function buddy_list() {
      return $this->ldap->employee_list('all');
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
    if(SEND_EMAIL) {
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
         kohana::log('info', "Sent Buddy Notification Email to [{$email_info['buddy_email']}] from [{$from}]");
      } else {
        kohana::log('error', "One or both To and From email addresses for the Notify Buddy email were invalid\n"
                ."To: {$email_info['buddy email']}\nFrom:{$from}");
      }
    } else {
      kohana::log('debug', "SEND_EMAIL is false so Buddy notification email not sent");
    }
    return $mail_sent;
  }

  
  

}