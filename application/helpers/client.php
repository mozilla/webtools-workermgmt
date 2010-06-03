<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Client functions for redirecting and for sending notices and warnings to the user.
 *
 * @author     	original: Ryan Snyder
 * @author      additions: Sam Keen
 * @version		v.0.2
 */
class client {


    private static $validation_results = null;
    

    /**
     * Retrieves messages intended for the user/client from a lower level function.
     * This is part of a message passing system which provides standard methods
     * for lower level functions to pass messages to the client (such as failed
     * input validation, etc).
     *
     * @see		self::clientMessageSend
     * @access	public
     * @static
     * @return	string	HTML output of user messages
     */
    public static function messageFetchHtml() {
        if ($messages = Session::instance()->get_once('client_messages')) {
            $notices  = '';
            $warnings = '';
            $errors   = '';
            foreach ($messages as $message) {
                switch ($message[1]) {
                    case E_USER_WARNING:
                        $warnings .= '<li>'.$message[0].'</li>'."\n";
                        break;
                    case E_USER_ERROR:
                        $errors .= '<li">'.$message[0].'</li>'."\n";
                        break;
                    case E_USER_NOTICE:
                    default:
                        $notices .= "
                                <div class=\"positive\">
                                        <span class=\"close\"><a href=\"#\" onclick=\"$(this).parents('div.positive').hide('slow');return false;\">Close</a></span>" . $message[0] . "</p>
                                </div>
                        ";
                        break;
                }
            }

            $message_html = "<div id=\"notifications\">\n";
            if (!empty($warnings) || !empty($errors)) {
                $message_html .= "
                        <div class=\"negative\">
                          <span class=\"close\"><a href=\"#\" onclick=\"$('#notifications').hide('slow');return false;\">Close</a></span>
                          <p>
                                Uh oh! Something went wrong...
                                <ul>
                                    {$warnings}{$errors}
                                </ul>
                          </p>
                        </div>
                ";
            }

            if (!empty($notices)) {
                $message_html .= $notices;
            }

            if (!empty($message_html)) {
                return $message_html."\n</div>\n";
            }
        }
    }

    /**
     * Stores a message intended for the user/client from a lower level function.
     * This is part of a message passing system which provides standard methods
     * for lower level functions to pass messages to the client (such as failed
     * input validation, etc).
     *
     * @see		self::messagesFetchHtml()
     * @access	public
     * @static
     * @param	string	Message to pass to the client
     * @param	int		Classification of message type - E_USER_WARNING, E_USER_ERROR, E_USER_NOTICE
     * @return	void
     */
    public static function messageSend($feedback, $type) {
        $feedback = is_array($feedback)?$feedback:array($feedback);
        $current_messages = Session::instance()->get('client_messages');
        foreach ($feedback as $message) {
            $current_messages[] = array($message, $type);
        }
        Session::instance()->set('client_messages',$current_messages);
    }

    /**
     * Stores a Kohana error array and prepares it for client display.
     *
     * @see		self::messagesFetchHtml()
     * @access	public
     * @static
     * @param 	array 	An array of Kohana errors that are returned from Kohana Validation
     * @param 	string 	The file and string prefix for the error.  For 'auth', requires i18n/auth.php,
     *					which contains - 'auth_email_required' => 'An email address is required'.
     * @return	void
     */
    public static function messageSendKohana(array $errors, $type='auth') {
        if (is_array($errors) && !empty($errors)) {
            foreach ($errors as $key => $value) {
                $message = Kohana::lang($type . '.'  . $type ."_" . $key . '_' . $value);
                self::messageSend($message, E_USER_WARNING);
            }
        }
    }
    /**
     * Set the results of the validation (from Validation_Core).
     * self::validation($input_key) then uses this to output those errors
     * at the specific form input element
     * 
     * @param array $results
     */
    public static function validation_results($results) {
        self::$validation_results = $results;
    }
    /**
     * Used in the UI to get vaidation text for given Form inputs
     *
     * @param string $input_key The form input name
     * @return string The validation text.
     */
    public static function validation($input_key) {
        if(isset(self::$validation_results[$input_key])&&!empty(self::$validation_results[$input_key])) {
            echo("<span class=\"error\">".htmlspecialchars(self::$validation_results[$input_key], ENT_NOQUOTES, 'UTF-8')."</span>");
        }
    }
    
    /*
     * Returns true if client is storing messages at E_USER_ERROR || E_USER_WARNING
     *
     * @return boolean
     */
    public static function has_errors() {
        $has_errors = false;
        if ($messages = Session::instance()->get('client_messages')) {
            foreach ($messages as $message) {
                if($message[1]==E_USER_ERROR || $message[1]==E_USER_WARNING){
                    $has_errors=true;
                    break;
                }
            }
        }
        return $has_errors;
    }


    /* */
}
