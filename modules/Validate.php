<?php
namespace Sbnc\Modules;

class Validate extends Module implements ModuleInterface {

    /**
     * Module options
     *
     * The array-value holds the form-field(s) name(s) for the
     * checks to be applied on. The checks are defined in the
     * key:
     *
     * email: check for a valid email
     * url:   check for a valid url
     *
     * @var array
     */
    private $errors = [
        'email'     => 'Email is not valid.',
        'url'       => 'URL is not valid.',
        'required'  => '%field% is required'
    ];

    private $options = [
        'email'    => ['email', 'mail'],
        'url'      => ['url', 'link', 'web'],
        'required' => ['email', 'name', 'message']
    ];

    public function check() {
        foreach ($this->options as $key => $value) {
            foreach ($value as $name) {
                if (isset($this->master['request'][$name])) {
                    $func = 'validate_' . $key;
                    $this->$func($this->master['request'][$name], $name);
                }
            }
        }
    }

    protected function validate_required($value, $name) {
        if (strlen(trim($value)) == 0) {
            $err = str_replace('%field%', $name, $this->errors['required']);
            array_push($this->master['errors'], $err);
            return false;
        }
        return true;
    }

    protected function validate_email($value, $name) {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            array_push($this->master['errors'], $this->errors['email']);
            return false;
        }
        return true;
    }

    protected function validate_ip($value, $name) {
        if (filter_var($value, FILTER_VALIDATE_IP) === false) {
            array_push($this->master['errors'], $this->errors['ip']);
            return false;
        }
        return true;
    }

    protected function validate_url($value, $name) {
        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            array_push($this->master['errors'], $this->errors['url']);
            return false;
        }
        return true;
    }

}