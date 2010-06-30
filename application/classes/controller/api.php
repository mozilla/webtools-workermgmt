<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * This controller handles the web dev forms.
 *
 * @author  skeen
 */
class Controller_Api extends Controller {

    protected $jsonp_callback = null;

    public function  before() {
        $this->json_callback = Arr::get($_GET, 'callback');
        parent::before();
    }

    /**
     * Landing page
     */
    public function action_all_employees() {
        $hiring = new Model_Hiring($this->get_ldap());
        $employees = Form_Helper::format_manager_list($hiring->buddy_list());
        foreach ($employees as $email => &$emp_label) {
           $emp_label .= " ({$email})";
        }
        $this->json_response($employees);

    }

    /**
     * json encoe the $data array applying jsonp callback if requested
     *
     * @param array $data
     */
    public function json_response($data) {
        $json_data = !is_resource($data)?json_encode($data):null;
        $json_data = $this->json_callback!==null
                ? "{$this->json_callback}({$json_data})"
                : $json_data;
        $this->request->response = $json_data;
    }
}