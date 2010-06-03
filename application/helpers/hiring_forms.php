<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * General helper for the forms involved in hiring.
 *
 * @author     	original: Sam Keen
 * @version		v.0.1
 * @license    http://www.opensource.org/licenses/mit-license.php
 */
class hiring_forms {


    /**
     * Foreach manager in array, builds their display name
     *  depending on what info is available.
     *
     * @param array $managers
     * @return array
     */
    public static function format_manager_list(array $managers) {
        foreach($managers as $manager_email => &$manager_info) {
            $manager_info = !empty($manager_info['title'])
                    ? "{$manager_info['cn']} - {$manager_info['title']}"
                    : $manager_info['cn'];
        }
        return array(''=>'Select...')+$managers;
    }
    /**
     * For non DB backed lookup lists used to populate UI elements like selects
     * and radio groups. THe submitted value is checked against the list array
     * need to in order to disallow values not in that list.
     *
     * @param array $select_lists This is the array af all the select lists for
     *  the controller. They are the lookup list that populated a select list or
     *  radio group in the UI.
     *  ex: $select_lists = array(
        'hire_type' => array(
            ""  => "Select ...",
            "Employee" => "Employee",
            "Intern" =>  "Intern"
        ),...
     * @return void
     */
    public static function filter_disallowed_values($select_lists) {
        foreach ($select_lists as $post_key => $select_list) {
            $submitted_value = isset($_POST[$post_key]) ? trim($_POST[$post_key]) : null;
            $_POST[$post_key]= key_exists($post_key, $select_lists) && key_exists($submitted_value, $select_lists[$post_key])
                ? $submitted_value
                : null;
        }
    }
}
