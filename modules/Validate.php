<?php
namespace sbnc\modules;

use sbnc\Sbnc;
use sbnc\core\Module;

class Validate extends Module
{

    ######################################################################################
    #########################           CONFIGURATION            #########################
    ######################################################################################

    /**
     * May be disabled if any inconsistencies occur
     *
     * @var bool Enable or disable module
     */
    protected $enabled = true;

    /**
     * Module options
     *
     * The array-value holds the form-field(s) name(s) for the
     * checks to be applied on. The checks are defined in the
     * value-array e.g.:
     *
     * - The form field with name "email" must be a correctly
     * formatted email address, is required and does not allow tags.
     *
     * - The form field with name "message" is required and
     * must have a minimum of 10 and no more than 1000 characters.
     * It also does not allow tags.
     *
     * - and so on...
     *
     * @var array Options
     */
    private $validations = [
        // used by example.php
        'email' => ['email', 'required', 'no_tags'],
        'name' => ['required', 'no_tags', 'min:4', 'max:30'],
        'message' => ['required', 'no_tags', 'min:10', 'max:1000'],

        // other examples
        'mail' => ['email', 'required'],
        'url' => ['url'],
        'link' => ['url'],
        'web' => ['url'],
        'ip' => ['ip'],
    ];

    /**
     * Define custom error messages for every rule defined in options.
     * You may use the %placeholders% to be replaced by the correct
     * values.
     *
     * @var array Custom errors
     */
    private $errors = [
        'email' => [
            'email' => 'Check your Email Address!',
            'required' => 'No Email Address given.'
        ],
        'name' => [
            'required' => 'What\'s your name?',
            'min' => 'Your name is too short. %min% characters minimum!',
            'max' => 'Your name is too long. %max% characters maximum!',
        ],
        'message' => [
            'required' => 'Please write something :-)',
            'min' => 'The message is too short. %min% characters minimum!',
            'max' => 'The message is too long. %max% characters maximum!',
            'no_tags' => 'Tags are not allowed in the message!'
        ]
    ];

    /**
     * default error messages to be used if you didn't define
     * a custom one.
     *
     * @var array Default errors
     */
    private $default_errors = [
        'no_tags' => '%field% does not allow tags',
        'email' => '%field% is not valid',
        'url' => '%field% is not valid',
        'required' => '%field% is required',
        'min' => '%field% must have a minimum of %min% characters',
        'max' => '%field% must not have more than %max% characters',
        'alpha_num' => '%field% only allows alphanumeric characters',
        'latin' => '%field% only allows latin characters',
        'latin_digits' => '%field% only allows latin characters and digits',
        'alpha' => '%field% only allows alpha characters',
        'digit' => '%field% may only contain digits',
        'numeric' => '%field% may only contain numeric values',
        'regex' => '%field% is not valid'
    ];

    ######################################################################################
    ######################################################################################


    protected function init()
    {

    }

    public function check()
    {
        foreach ($this->validations as $key => $value) {
            foreach ($value as $validator) {
                $val = Sbnc::request($key);
                if ($val !== null) {
                    if (strcmp($validator, 'required') !== 0 && empty($val)) continue;
                    $data = explode(':', $validator);
                    $validator = $data[0];
                    array_shift($data);
                    $func = 'validate_' . $validator;
                    $this->$func($val, $key, $data);
                }
            }
        }
    }

    protected function validate_no_tags($value, $name, $options)
    {
        if ($value != strip_tags($value)) {
            if (isset($this->errors[$name]['no_tags'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['no_tags']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['no_tags']);
            }
            Sbnc::addError($err);
            return false;
        }
        return true;
    }

    protected function validate_required($value, $name, $options)
    {
        if (strlen(trim($value)) == 0) {
            if (isset($this->errors[$name]['required'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['required']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['required']);
            }
            Sbnc::addError($err);
            return false;
        }
        return true;
    }

    protected function validate_email($value, $name, $options)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            if (isset($this->errors[$name]['email'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['email']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['email']);
            }
            Sbnc::addError($err);
            return false;
        }
        return true;
    }

    protected function validate_ip($value, $name, $options)
    {
        if (filter_var($value, FILTER_VALIDATE_IP) === false) {
            if (isset($this->errors[$name]['ip'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['ip']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['ip']);
            }
            Sbnc::addError($err);
            return false;
        }
        return true;
    }

    protected function validate_url($value, $name, $options)
    {
        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            if (isset($this->errors[$name]['url'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['url']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['url']);
            }
            Sbnc::addError($err);
            return false;
        }
        return true;
    }

    protected function validate_min($value, $name, $options)
    {
        if (strlen($value) < $options[0]) {
            if (isset($this->errors[$name]['min'])) {
                $err = str_replace(['%field%', '%min%'], [$name, $options[0]], $this->errors[$name]['min']);
            } else {
                $err = str_replace(['%field%', '%min%'], [$name, $options[0]], $this->default_errors['min']);
            }
            Sbnc::addError($err);
            return false;
        }
        return true;
    }

    protected function validate_max($value, $name, $options)
    {
        if (strlen($value) > $options[0]) {
            if (isset($this->errors[$name]['max'])) {
                $err = str_replace(['%field%', '%max%'], [$name, $options[0]], $this->errors[$name]['max']);
            } else {
                $err = str_replace(['%field%', '%max%'], [$name, $options[0]], $this->default_errors['max']);
            }
            Sbnc::addError($err);
            return false;
        }
        return true;
    }

    protected function validate_alpha_num($value, $name, $options)
    {
        if (!ctype_alnum($value)) {
            if (isset($this->errors[$name]['alpha_num'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['alpha_anum']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['alpha_num']);
            }
            Sbnc::addError($err);
            return false;
        }
        return true;
    }

    protected function validate_latin($value, $name, $options)
    {
        if (!preg_match('/^[\p{Latin}]+$/', $value)) {
            if (isset($this->errors[$name]['latin'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['latin']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['latin']);
            }
            Sbnc::addError($err);
            return false;
        }
        return true;
    }

    protected function validate_latin_digits($value, $name, $options)
    {
        if (!preg_match('/^[\p{Latin}[0-9]+$/', $value)) {
            if (isset($this->errors[$name]['latin_digits'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['latin_digits']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['latin_digits']);
            }
            Sbnc::addError($err);
            return false;
        }
        return true;
    }

    protected function validate_alpha($value, $name, $options)
    {
        if (!ctype_alpha($value)) {
            if (isset($this->errors[$name]['alpha'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['alpha']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['alpha']);
            }
            Sbnc::addError($err);
            return false;
        }
        return true;
    }

    protected function validate_digit($value, $name, $options)
    {
        if (!ctype_digit($value)) {
            if (isset($this->errors[$name]['digit'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['digit']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['digit']);
            }
            Sbnc::addError($err);
            return false;
        }
        return true;
    }

    protected function validate_numeric($value, $name, $options)
    {
        if (!is_numeric($value)) {
            if (isset($this->errors[$name]['numeric'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['numeric']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['numeric']);
            }

            Sbnc::addError($err);
            return false;
        }
        return true;
    }

    protected function validate_regex($value, $name, $options)
    {
        $regex = $options[0];
        if (count($options) > 1) foreach ($options as $option) $regex .= $option;
        if (!preg_match($regex, $value)) {
            if (isset($this->errors[$name]['regex'])) {
                $err = str_replace('%field%', $name, $this->errors[$name]['regex']);
            } else {
                $err = str_replace('%field%', $name, $this->default_errors['regex']);
            }
            Sbnc::addError($err);
            return false;
        }
        return true;
    }

}