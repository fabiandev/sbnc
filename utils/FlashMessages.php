<?php
namespace sbnc\utils;

use sbnc\Sbnc;

class FlashMessages extends Util implements UtilInterface
{

    public $cache = [];

    protected $options = [
        'session_name' => 'sbnc_flash'
    ];

    public function set_namespace($session_name)
    {
        $this->options['session_name'] = $session_name;
    }

    protected function init()
    {
        if (session_status() == PHP_SESSION_DISABLED) $nosess = true;
        if (session_status() == PHP_SESSION_NONE && headers_sent()) $nosess = true;
        if (session_status() == PHP_SESSION_ACTIVE || session_start()) $this->enabled = true;
        if (isset($nosess) && $nosess == true) {
            throw new \Exception('Session could not be created. Be sure you started sbnc before any other output');
        }
        unset($_SESSION[$this->options['session_name']]['_CACHE_']);
    }

    public function before()
    {
        if (isset($_SESSION[$this->options['session_name']])) {
            $this->cache = $_SESSION[$this->options['session_name']];
        }
    }

    public function flash($type, $value, $key = null)
    {
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

    public function get($type, $key = null)
    {
        if (!$this->enabled) return false;
        if ($key === null) {
            if (isset($_SESSION[$this->options['session_name']][$type])) {
                $flash = $_SESSION[$this->options['session_name']][$type];
                unset($_SESSION[$this->options['session_name']][$type]);
                return $flash;
            } elseif (isset($this->cache[$type])) {
                return $this->cache[$type];
            } else {
                return [];
            }
        } else {
            if (isset($_SESSION[$this->options['session_name']][$type][$key])) {
                $flash = $_SESSION[$this->options['session_name']][$type][$key];
                unset($_SESSION[$this->options['session_name']][$type][$key]);
                return $flash;
            } elseif (isset($this->cache[$type][$key])) {
                return $this->cache[$type][$key];
            } else {
                return '';
            }
        }
    }

    public function get_once($type, $key = null)
    {
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

    public function get_safe($type, $key = null)
    {
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

    public function get_cache($type, $key = null)
    {
        if (!$this->enabled) return false;
        if ($key === null) {
            if (isset($this->cache[$type])) {
                $cache = $this->cache[$type];
                return $cache;
            } else {
                return [];
            }
        } else {
            if (isset($this->cache[$type][$key])) {
                $cache = $this->cache[$type][$key];
                return $cache;
            } else {
                return '';
            }
        }
    }

    public function is_set($type, $key = null)
    {
        if (!$this->enabled) return false;
        if ($key === null) {
            if (isset($_SESSION[$this->options['session_name']][$type]) || isset($this->cache[$type])) {
                return true;
            } else {
                return false;
            }
        } else {
            if (isset($_SESSION[$this->options['session_name']][$type][$key]) || isset($this->cache[$type][$key])) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function in_session($type, $key = null)
    {
        if (!$this->enabled) return false;
        if ($key === null) {
            if (isset($_SESSION[$this->options['session_name']][$type])) {
                return true;
            } else {
                return false;
            }
        } else {
            if (isset($_SESSION[$this->options['session_name']][$type][$key])) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function remove($type, $key = null)
    {
        if (!$this->enabled) return false;
        if ($key === null) {
            if (isset($_SESSION[$this->options['session_name']][$type])) {
                unset($_SESSION[$this->options['session_name']][$type]);
                return true;
            } else {
                return false;
            }
        } else {
            if (isset($_SESSION[$this->options['session_name']][$type][$key])) {
                unset($_SESSION[$this->options['session_name']][$type][$key]);
                return true;
            } else {
                return false;
            }
        }
    }

    public function remove_cached($type, $key = null)
    {
        if (!$this->enabled) return false;
        if ($key === null) {
            if (isset($this->cache[$type])) {
                unset($this->cache[$type]);
                return true;
            } else {
                return false;
            }
        } else {
            if (isset($this->cache[$type][$key])) {
                unset($this->cache[$type][$key]);
                return true;
            } else {
                return false;
            }
        }
    }

    public function remove_all($type, $key = null)
    {
        $this->remove($type, $key);
        $this->remove_cached($type, $key);
    }

    public function flush()
    {
        unset($_SESSION[$this->options['session_name']]);
    }

    public function count($type)
    {
        if (!$this->enabled) return false;
        if (isset($_SESSION[$this->options['session_name']][$type]) &&
            is_array($_SESSION[$this->options['session_name']][$type])
        ) {
            return count($_SESSION[$this->options['session_name']][$type]);
        } elseif (isset($this->cache[$type]) && is_array($this->cache[$type])) {
            return count($this->cache[$type]);
        } else {
            return false;
        }
    }

} 