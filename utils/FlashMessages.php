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
     * Module may be disabled if an inconsistency occurs
     *
     * @var bool Enable or disable module
     */
    protected $enabled = true;

    /**
     * Default namespace
     *
     * @var string Default namespace
     */
    private $namespace = 'sbnc';

    ######################################################################################
    ######################################################################################

    public $cache = [];

    protected function init()
    {
        if (!$this->isEnabled()) return;
        if (session_status() == PHP_SESSION_DISABLED) $nosess = true;
        if (session_status() == PHP_SESSION_NONE && headers_sent()) $nosess = true;
        if (session_status() != PHP_SESSION_ACTIVE && !session_start()) {
            $this->enabled = false;
            Sbnc::printException(new \Exception('Headers have already been sent. Make sure to include sbnc before any other output'));
        }
        if (isset($nosess) && $nosess == true) {
            throw new \Exception('Session could not be created. Be sure you started sbnc before any other output');
        }
        unset($_SESSION[$this->namespace]['_CACHE_']);
    }

    public function before()
    {
        if (!$this->isEnabled()) return;
        if (isset($_SESSION[$this->namespace])) {
            $this->cache = $_SESSION[$this->namespace];
        }
    }

    public function flash($type, $name, $value)
    {
        if (!$this->isEnabled()) return false;
        $_SESSION[$this->namespace][$type][$name] = $value;
        return true;
    }

    public function push($type, $name, $value)
    {
        if (!$this->isEnabled()) return false;
        if (isset($_SESSION[$this->namespace][$type][$name])) {
            $data = $_SESSION[$this->namespace][$type][$name];
            if (is_array($data)) {
                array_push($_SESSION[$this->namespace][$type][$name], $value);
            } else {
                return false;
            }
        }
        return true;
    }

    public function get($type, $name = null)
    {
        if (!$this->isEnabled()) return false;

        if ($name == null) {
            if (isset($_SESSION[$this->namespace][$type])) {
                $flash = $_SESSION[$this->namespace][$type];
                unset($_SESSION[$this->namespace][$type]);
                return $flash;
            } elseif (isset($this->cache[$type])) {
                return $this->cache[$type];
            }
        } else {
            if (isset($_SESSION[$this->namespace][$type][$name])) {
                $flash = $_SESSION[$this->namespace][$type][$name];
                unset($_SESSION[$this->namespace][$type][$name]);
                return $flash;
            } elseif (isset($this->cache[$type][$name])) {
                return $this->cache[$type][$name];
            }
        }
        return null;
    }

    public function getOnce($type, $name = null)
    {
        if (!$this->isEnabled()) return false;
        if ($name == null) {
            if (isset($_SESSION[$this->namespace][$type])) {
                $flash = $_SESSION[$this->namespace][$type];
                unset($_SESSION[$this->namespace][$type]);
                return $flash;
            }
        } else {
            if (isset($_SESSION[$this->namespace][$type][$name])) {
                $flash = $_SESSION[$this->namespace][$type][$name];
                unset($_SESSION[$this->namespace][$type][$name]);
                return $flash;
            }
        }
        return null;
    }

    public function getSafe($type, $name = null)
    {
        if (!$this->isEnabled()) return false;

        if ($name == null) {
            if (isset($_SESSION[$this->namespace][$type])) {
                $flash = $_SESSION[$this->namespace][$type];
                return $flash;
            } elseif (isset($this->cache[$type])) {
                return $this->cache[$type];
            }
        } else {
            if (isset($_SESSION[$this->namespace][$type][$name])) {
                $flash = $_SESSION[$this->namespace][$type][$name];
                return $flash;
            } elseif (isset($this->cache[$type][$name])) {
                return $this->cache[$type][$name];
            }
        }
        return null;
    }

    public function getCached($type, $name = null)
    {
        if (!$this->isEnabled()) return false;

        if ($name == null) {
            if (isset($this->cache[$type])) {
                return $this->cache[$type];
            }
        } else {
            if (isset($this->cache[$type][$name])) {
                return $this->cache[$type][$name];
            }
        }
        return null;
    }

    public function exists($type, $name = null)
    {
        if (!$this->isEnabled()) return false;

        if ($name == null) {
            if (isset($_SESSION[$this->namespace][$type]) || isset($this->cache[$type])) {
                return true;
            }
        } else {
            if (isset($_SESSION[$this->namespace][$type][$name]) || isset($this->cache[$type][$name])) {
                return true;
            }
        }
        return false;
    }

    public function inSession($type, $name = null)
    {
        if (!$this->isEnabled()) return false;
        if ($name == null) {
            if (isset($_SESSION[$this->namespace][$type])) {
                return true;
            }
        } else {
            if (isset($_SESSION[$this->namespace][$type][$name])) {
                return true;
            }
        }
        return false;
    }

    public function remove($type, $name = null)
    {
        if (!$this->isEnabled()) return false;
        if ($name == null) {
            if (isset($_SESSION[$this->namespace][$type])) {
                unset($_SESSION[$this->namespace][$type]);
                return true;
            }
        } else {
            if (isset($_SESSION[$this->namespace][$type][$name])) {
                unset($_SESSION[$this->namespace][$type][$name]);
                return true;
            }
        }
        return false;
    }

    public function removeCached($type, $name = null)
    {
        if (!$this->isEnabled()) return false;
        if ($name == null) {
            if (isset($this->cache[$type])) {
                unset($this->cache[$type]);
                return true;
            }
        } else {
            if (isset($this->cache[$type][$name])) {
                unset($this->cache[$type][$name]);
                return true;
            }
        }
        return false;
    }

    public function removeAll($type, $name = null)
    {
        if (!$this->isEnabled()) return;
        $this->remove($type, $name);
        $this->removeCached($type, $name);
    }

    public function flush($type = null)
    {
        if (!$this->isEnabled()) return;
        if ($type == null) {
            if (isset($_SESSION[$this->namespace])) {
                unset($_SESSION[$this->namespace]);
            }
        } else {
            if (isset($_SESSION[$this->namespace][$type])) {
                unset($_SESSION[$this->namespace][$type]);
            }
        }
    }

    public function count($type, $name = null)
    {
        if (!$this->isEnabled()) return false;
        if ($name == null) {
            if (isset($_SESSION[$this->namespace][$type])) {
                $data = $_SESSION[$this->namespace][$type];
                if (is_array($data)) {
                    return count($data);
                }
            } elseif (isset($this->cache[$type])) {
                $data = $this->cache[$type];
                if (is_array($data)) {
                    return count($data);
                }
            }
        } else {
            if (isset($_SESSION[$this->namespace][$type][$name])) {
                $data = $_SESSION[$this->namespace][$type][$name];
                if (is_array($data)) {
                    return count($data);
                }
            } elseif (isset($this->cache[$type][$name])) {
                $data = $this->cache[$type][$name];
                if (is_array($data)) {
                    return count($data);
                }
            }
        }
        return 0;
    }

} 