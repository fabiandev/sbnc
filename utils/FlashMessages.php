<?php
namespace sbnc\utils;

use sbnc\Sbnc;

/**
 * Class FlashMessages
 *
 * Provides the implementation for flash messages
 *
 * @package sbnc\utils
 */
class FlashMessages extends Util implements UtilInterface
{

    ######################################################################################
    #########################           CONFIGURATION            #########################
    ######################################################################################

    /**
     * Default namespace
     *
     * @var string Default namespace
     */
    private $session_name = 'sbnc_flash';

    ######################################################################################
    ######################################################################################


    /**
     * Changes the namespace (but does not copy existing entries!)
     *
     * @param $session_name
     */
    public function setNamespace($session_name)
    {
        $this->session_name = $session_name;
    }

    public $cache = [];

    protected function init()
    {
        if (session_status() == PHP_SESSION_DISABLED) $nosess = true;
        if (session_status() == PHP_SESSION_NONE && headers_sent()) $nosess = true;
        if (session_status() == PHP_SESSION_ACTIVE || session_start()) {
            $this->enabled = true;
        } else {
            Sbnc::printException(new \Exception('Headers have already been sent. Make sure to include sbnc before any other output'));
        }
        if (isset($nosess) && $nosess == true) {
            throw new \Exception('Session could not be created. Be sure you started sbnc before any other output');
        }
        unset($_SESSION[$this->session_name]['_CACHE_']);
    }

    public function before()
    {
        if (isset($_SESSION[$this->session_name])) {
            $this->cache = $_SESSION[$this->session_name];
        }
    }

    public function flash($type, $value, $key = null)
    {
        if (!$this->enabled) return false;
        if ($key === null) {
            if (is_array($value)) {
                $_SESSION[$this->session_name][$type] = $value;
            } else {
                $_SESSION[$this->session_name][$type][] = $value;
            }
        } else {
            $_SESSION[$this->session_name][$type][$key] = $value;
        }
        return true;
    }

    public function get($type, $key = null)
    {
        if (!$this->enabled) return false;
        if ($key === null) {
            if (isset($_SESSION[$this->session_name][$type])) {
                $flash = $_SESSION[$this->session_name][$type];
                unset($_SESSION[$this->session_name][$type]);
                return $flash;
            } elseif (isset($this->cache[$type])) {
                return $this->cache[$type];
            } else {
                return [];
            }
        } else {
            if (isset($_SESSION[$this->session_name][$type][$key])) {
                $flash = $_SESSION[$this->session_name][$type][$key];
                unset($_SESSION[$this->session_name][$type][$key]);
                return $flash;
            } elseif (isset($this->cache[$type][$key])) {
                return $this->cache[$type][$key];
            } else {
                return '';
            }
        }
    }

    public function getOnce($type, $key = null)
    {
        if (!$this->enabled) return false;
        if ($key === null) {
            if (isset($_SESSION[$this->session_name][$type])) {
                $flash = $_SESSION[$this->session_name][$type];
                unset($_SESSION[$this->session_name][$type]);
                return $flash;
            } else {
                return [];
            }
        } else {
            if (isset($_SESSION[$this->session_name][$type][$key])) {
                $flash = $_SESSION[$this->session_name][$type][$key];
                unset($_SESSION[$this->session_name][$type][$key]);
                return $flash;
            } else {
                return '';
            }
        }
    }

    public function getSafe($type, $key = null)
    {
        if (!$this->enabled) return false;
        if ($key === null) {
            if (isset($_SESSION[$this->session_name][$type])) {
                $flash = $_SESSION[$this->session_name][$type];
                return $flash;
            } else {
                return [];
            }
        } else {
            if (isset($_SESSION[$this->session_name][$type][$key])) {
                $flash = $_SESSION[$this->session_name][$type][$key];
                return $flash;
            } else {
                return '';
            }
        }
    }

    public function getCached($type, $key = null)
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

    public function exists($type, $key = null)
    {
        if (!$this->enabled) return false;
        if ($key === null) {
            if (isset($_SESSION[$this->session_name][$type]) || isset($this->cache[$type])) {
                return true;
            } else {
                return false;
            }
        } else {
            if (isset($_SESSION[$this->session_name][$type][$key]) || isset($this->cache[$type][$key])) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function inSession($type, $key = null)
    {
        if (!$this->enabled) return false;
        if ($key === null) {
            if (isset($_SESSION[$this->session_name][$type])) {
                return true;
            } else {
                return false;
            }
        } else {
            if (isset($_SESSION[$this->session_name][$type][$key])) {
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
            if (isset($_SESSION[$this->session_name][$type])) {
                unset($_SESSION[$this->session_name][$type]);
                return true;
            } else {
                return false;
            }
        } else {
            if (isset($_SESSION[$this->session_name][$type][$key])) {
                unset($_SESSION[$this->session_name][$type][$key]);
                return true;
            } else {
                return false;
            }
        }
    }

    public function removeCached($type, $key = null)
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

    public function removeAll($type, $key = null)
    {
        $this->remove($type, $key);
        $this->removeCached($type, $key);
    }

    public function flush()
    {
        unset($_SESSION[$this->session_name]);
    }

    public function count($type)
    {
        if (!$this->enabled) return false;
        if (isset($_SESSION[$this->session_name][$type]) &&
            is_array($_SESSION[$this->session_name][$type])
        ) {
            return count($_SESSION[$this->session_name][$type]);
        } elseif (isset($this->cache[$type]) && is_array($this->cache[$type])) {
            return count($this->cache[$type]);
        } else {
            return false;
        }
    }

} 