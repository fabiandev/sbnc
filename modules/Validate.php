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
    private $default_errors = [
        'email'          => '%field% is not valid',
        'url'            => '%field% is not valid',
        'required'       => '%field% is required',
        'min'            => '%field% must have a minimum of %min% characters',
        'max'            => '%field% must not have more than %max% characters',
        'alphanum'       => '%field% only allows alphanumeric characters',
        'latin'          => '%field% only allows latin characters',
        'latindigits'    => '%field% only allows latin characters and digits',
        'alpha'          => '%field% only allows alpha characters',
        'digit'          => '%field% may only contain digits',
        'numeric'        => '%field% may only contain numeric values',
        'regex'          => '%field% is not valid'
    ];

    private $errors = [
        'email'   => [
            'email'    => 'Check your Email Address!',
            'required' => 'No Email Address given.'
        ],
        'name'    => [
            'required' => 'What\'s your name?',
            'min'      => 'Your name is too short. %min% characters minimum!',
            'max'      => 'Your name is too long. %max% characters maximum!',
        ],
        'message' => [
            'required' => 'Please write something :-)',
            'min'      => 'The message is too short. %min% characters minimum!',
            'max'      => 'The message is too long. %max% characters maximum!'
        ]
    ];

    private $options = [
        // used by example.php
        'email'   => ['email', 'required'],
        'name'    => ['required', 'min:4', 'max:30'],
        'message' => ['required', 'min:10', 'max:1000'],

        // other examples:
        'mail'    => ['email', 'required'],
        'url'     => ['url'],
        'link'    => ['url'],
        'web'     => ['url'],
        'ip'      => ['ip'],
    ];

    protected function init() {
        $this->enabled = true;
    }

    public function check() {
        foreach ($this->options as $key => $value) {
            foreach ($value as $validator) {
                if (isset($this->master['request'][$key])) {
                    if (strcmp($validator, 'required') !== 0 && empty($this->master['request'][$key])) continue;
                    $data = explode(':', $validator);
                    $validator = $data[0];
                    array_shift($data);
                    $func = 'validate_' . $validator;
                    $this->$func($this->master['request'][$key], $key, $data);
                }
            }
        }
    }

    protected function validate_required($value, $name, $options) {
        if (strlen(trim($value)) == 0) {
            if (isset($this->errors[$name]['required'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['required']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['required']);
            }
            array_push($this->master['errors'], $err);
            return false;
        }
        return true;
    }

    protected function validate_email($value, $name, $options) {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            if (isset($this->errors[$name]['email'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['email']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['email']);
            }
            array_push($this->master['errors'], $err);
            return false;
        }
        return true;
    }

    protected function validate_ip($value, $name, $options) {
        if (filter_var($value, FILTER_VALIDATE_IP) === false) {
            if (isset($this->errors[$name]['ip'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['ip']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['ip']);
            }
            array_push($this->master['errors'], $err);
            return false;
        }
        return true;
    }

    protected function validate_url($value, $name, $options) {
        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            if (isset($this->errors[$name]['url'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['url']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['url']);
            }
            array_push($this->master['errors'], $err);
            return false;
        }
        return true;
    }

    protected function validate_min($value, $name, $options) {
        if (strlen($value) < $options[0]) {
            if (isset($this->errors[$name]['min'])) {
                $err = str_replace(['%field%', '%min%'], [$name, $options[0]], $this->errors[$name]['min']);
            } else {
                $err = str_replace(['%field%', '%min%'], [$name, $options[0]], $this->default_errors['min']);
            }
            array_push($this->master['errors'], $err);
            return false;
        }
        return true;
    }

    protected function validate_max($value, $name, $options) {
        if (strlen($value) > $options[0]) {
            if (isset($this->errors[$name]['max'])) {
                $err = str_replace(['%field%', '%max%'], [$name, $options[0]], $this->errors[$name]['max']);
            } else {
                $err = str_replace(['%field%', '%max%'], [$name, $options[0]], $this->default_errors['max']);
            }
            array_push($this->master['errors'], $err);
            return false;
        }
        return true;
    }

    protected function validate_alphanum($value, $name, $options) {
        if (!ctype_alnum($value)) {
            if (isset($this->errors[$name]['alphanum'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['alphanum']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['alphanum']);
            }
            array_push($this->master['errors'], $err);
            return false;
        }
        return true;
    }

    protected function validate_latin($value, $name, $options) {
        if (!preg_match('/^[\p{Latin}]+$/', $value)) {
            if (isset($this->errors[$name]['latin'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['latin']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['latin']);
            }
            array_push($this->master['errors'], $err);
            return false;
        }
        return true;
    }

    protected function validate_latindigits($value, $name, $options) {
        if (!preg_match('/^[\p{Latin}[0-9]+$/', $value)) {
            if (isset($this->errors[$name]['latin'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['latindigits']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['latindigits']);
            }
            array_push($this->master['errors'], $err);
            return false;
        }
        return true;
    }

    protected function validate_alpha($value, $name, $options) {
        if (!ctype_alpha($value)) {
            if (isset($this->errors[$name]['alpha'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['alpha']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['alpha']);
            }
            array_push($this->master['errors'], $err);
            return false;
        }
        return true;
    }

    protected function validate_digit($value, $name, $options) {
        if (!ctype_digit($value)) {
            if (isset($this->errors[$name]['digit'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['digit']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['digit']);
            }
            array_push($this->master['errors'], $err);
            return false;
        }
        return true;
    }

    protected function validate_numeric($value, $name, $options) {
        if (!is_numeric($value)) {
            if (isset($this->errors[$name]['numeric'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['numeric']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['numeric']);
            }

            array_push($this->master['errors'], $err);
            return false;
        }
        return true;
    }

    protected function validate_regex($value, $name, $options) {
        $regex = $options[0];
        if (count($options) > 1) foreach ($options as $option) $regex += $option;
        if (!preg_match($regex, $value)) {
            if (isset($this->errors[$name]['regex'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['regex']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['regex']);
            }
            array_push($this->master['errors'], $err);
            return false;
        }
        return true;
    }

}