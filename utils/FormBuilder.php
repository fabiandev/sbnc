<?php
namespace Sbnc\Utils;

// TODO: not ready to use!

class FormBuilder extends Util implements UtilInterface {

    private $form = [];

    protected function init() {}

    public function add_field($type, $value, $name, $id, $required) {
        $field = [
            'type'     => $type,
            'value'    => $value,
            'name'     => $name,
            'id'       => $id,
            'required' => $required
        ];
        array_push($this->form, $field);
    }

    public function get_form() {
        $code = '';
        foreach ($this->form as $key => $field) {
            if (strcmp('type', 'textarea') == 0) {

            } elseif (strcmp('type', 'radio') == 0) {

            } elseif (strcmp('type', 'checkbox') == 0) {

            } else {
                $code .= '<input type="'.$field['type'].'" id="'.$field['id'].'" name="'.$field['name'].'" value="'.$field['value'].'">';
            }
        }
        return $code;
    }

    public function print_form() {
        echo $this->get_form();
    }

} 