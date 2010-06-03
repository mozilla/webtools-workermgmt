<?php defined('SYSPATH') or die('No direct script access.');

class form extends form_Core {

    private static $required_fields = array();

    /**
     * sets the fields that are required.  UI then uses self::field_required($field_name)
     * to decorate those form inputs
     *
     * @param array $fields
     */
    public static function required_fields(array $fields) {
        self::$required_fields = $fields;
    }

    public static function field_required($field_name) {
        return in_array($field_name, self::$required_fields);
    }
    /**
     * For example, if called thusly:
     *
     *   auto_label('first_name');
     *
     * (and 'first_name' is in self::required_fields), it will render
     * 
     *   <label class="required" for="first_name">First Name</label>
     *
     * @param string/array $for_input label “for” name or an array of HTML attributes
     * @param string $text_label label text or HTML
     * @param string $attibutes a string to be attached to the end of the attributes
     * @return string The rendered label element
     */
    public static function auto_label($data, $text = null, $extra = null) {
        $for_input = $data;
        if(is_array($data)) {
            $for_input = isset($data['for'])?$data['for']:'';
        }
        if(self::field_required($for_input)) {
            if($extra && strstr($extra, 'class=')) {
                $extra = str_replace('class="', 'class="required ', $extra);
            } else {
                $extra = 'class="required"';
            }
        }
        $text = $text === null ? implode(' ', array_map('ucfirst',explode('_',$for_input))): $text ;
        return self::label($data, $text, $extra);
    }

}
