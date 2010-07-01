<?php defined('SYSPATH') or die('No direct script access.');

class Form extends Kohana_Form {

    /**
     * Keep the required fields ('not_empty') as a list so the Form class
     * can decorate them in the UI (adds class='required ..')
     *
     * Usage in Controller:
     * form::required_fields(array('hire_type','first_name','last_name',...)
     *
     * @todo Make this more automagic, so dont have to maintain a seperate array
     */
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
    /**
     *
     * @param string $field_name
     * @return boolean If this field is considered required or not
     */
    public static function field_required($field_name) {
        return in_array($field_name, self::$required_fields);
    }
    
    /**
     * Helper method for rendering lables.  Support the self::required_fields array
     * so req fields are rendered with class="required ..."
     *
     * Usage:
     *   echo auto_label('first_name');
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

    /**
     * Helper to render csrf tokens for forms
     * @return string Rendered hidden field
     */
    public static function csrf_token() {
        return self::hidden('csrf_token',$_SESSION['csrf_token'] = uniqid());
    }
    /**
     * Does the csrf check
     * @return boolean If token submitted matches the one in Session
     */
    public static function valid_token() {
        return Arr::get($_POST,'csrf_token')
            && (Arr::get($_POST,'csrf_token') == Arr::get($_SESSION,'csrf_token'));
    }

    /**
	 * Since K3 does not render id="$name" to match name="$name" (K2 did),
     * we override these methods to support the syntax:
     *
     *  form::input('first_name=id',...
     *
     * When =id is seen, in this case we would set $name = 'first_name', and
     * $attributes['id']='first_name' then call the return the parent method
     *
	 */
	public static function input($name, $value = NULL, array $attributes = NULL) {
		self::set_id_name($name, $attributes);
        return parent::input($name, $value, $attributes);
	}
    public static function label($input, $text = NULL, array $attributes = NULL) {
        self::set_id_name($name, $attributes);
        return parent::label($input, $text, $attributes);
    }
    public static function select($name, array $options = NULL, $selected = NULL, array $attributes = NULL) {
        self::set_id_name($name, $attributes);
        return parent::select($name, $options, $selected, $attributes);
    }
    public static function textarea($name, $body = '', array $attributes = NULL, $double_encode = TRUE) {
        self::set_id_name($name, $attributes);
        return parent::textarea($name, $body, $attributes, $double_encode);
    }
    private static function set_id_name(&$name, &$attributes) {
        if(substr($name,-3,3)=='=id') {
            $attributes['id'] = substr($name,0,-3);
            $name = substr($name,0,-3);
        }
    }

    /**
     * Create a Check box group.  Alternative UI widget to the Muti-select
     * <select> element
     * 
     * @param string $name
     * @param array $options
     * @param array $selected
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
