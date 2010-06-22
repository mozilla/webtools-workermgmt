<?php defined('SYSPATH') or die('No direct script access.');

class Form extends Kohana_Form {

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
     * @param string/array $for_input label for name or an array of HTML attributes
     * @param string $display_label label text or HTML
     * @param array $attibutes 
     * @return string The rendered label element
     */
    public static function auto_label($data, $display_label = null, array $extra = null) {
        $for_input = $data;
        if(is_array($data)) {
            $for_input = isset($data['for'])?$data['for']:'';
        }
        if(self::field_required($for_input)) {
            if($extra && key_exists('class', $extra)) {
                $extra['class'] = "{$extra['class']} required";
            } else {
                $extra['class'] = "required";
            }
        }
        $display_label = $display_label === null
            ? implode(' ', array_map('ucfirst',explode('_',$for_input)))
            : $display_label ;
        return self::label($data, $display_label, $extra);
    }

    public static function csrf_token() {
        return self::hidden('csrf_token',$_SESSION['csrf_token'] = uniqid());
    }

    public static function valid_token() {
        return Arr::get($_POST,'csrf_token')
            && (Arr::get($_POST,'csrf_token') == Arr::get($_SESSION,'csrf_token'));
    }

    /**
     *
     * @param <type> $name
     * @param array $options
     * @param <type> $selected
     * @param array $attributes
     * @return string 
     */
    public static function check_group($name, array $options = NULL, $selected = array(), array $attributes = NULL) {
        // $checkbox = $this->checkbox($name, $value, $checked, $attributes);
        // Set the input name
        $checkboxes = array();

        if (empty($options)) {
            // There are no options
            $options = array();
        } else {
            $selected = is_array($selected)?$selected:array((string)$selected);
            foreach ($options as $identifier => $label) {
                $checked = key_exists($identifier, $selected);
                $checkboxes[] = '<p>'.self::checkbox("{$name}[]", $identifier, $checked, $attributes)." {$label}</p>";
            }
            // Compile the options into a single string
            $checkboxes = "\n".implode("\n", $checkboxes)."\n";
        }

        return $checkboxes;




    }

}
