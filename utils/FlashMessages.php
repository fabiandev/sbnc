<?php
namespace Sbnc\Utils;


class FlashMessages extends Util implements UtilInterface {

    protected $enabled = false;

    protected $options = [
        'session_name'      => 'sbnc_flash'
    ];

    protected function init() {
        if (session_status() == PHP_SESSION_DISABLED) return;
        if (session_status() == PHP_SESSION_NONE && headers_sent()) return;
        if (session_start()) $this->enabled = true;
    }

    public function flash($type, $value, $key = null) {
        if (!$this->enabled) return false;
        if ($key === null) {
            if (is_array($value)) {
                $_SESSION[$this->options['session_name']][$type] = $value;
            } else {
                $_SESSION[$this->options['session_name']][$type][] = $value;
            }
        } else {
            $_SESSION[$this->options['session_name']][$type][$key] = $value;
        }
        return true;
    }

    public function get($type, $key = null) {
        if (!$this->enabled) return false;
        if ($key === null) {
            if (isset($_SESSION[$this->options['session_name']][$type])) {
                $flash = $_SESSION[$this->options['session_name']][$type];
                unset($_SESSION[$this->options['session_name']][$type]);
                return $flash;
            } else {
                return [];
            }
        } else {
            if (isset($_SESSION[$this->options['session_name']][$type][$key])) {
                $flash = $_SESSION[$this->options['session_name']][$type][$key];
                unset($_SESSION[$this->options['session_name']][$type][$key]);
                return $flash;
            } else {
                return '';
            }
        }
    }

    public function get_safe($type, $key = null) {
        if (!$this->enabled) return false;
        if ($key === null) {
            if (isset($_SESSION[$this->options['session_name']][$type])) {
                $flash = $_SESSION[$this->options['session_name']][$type];
                return $flash;
            } else {
                return [];
            }
        } else {
            if (isset($_SESSION[$this->options['session_name']][$type][$key])) {
                $flash = $_SESSION[$this->options['session_name']][$type][$key];
                return $flash;
            } else {
                return '';
            }
        }
    }

    public function count($type) {
        if (!$this->enabled) return false;
        if (isset($_SESSION[$this->options['session_name']][$type])) {
            return count($_SESSION[$this->options['session_name']][$type]);
        } else {
            return false;
        }
    }

    public function is_enabled() {
        return $this->enabled;
    }

    public function is_disabled() {
        return !$this->is_enabled();
    }

} 