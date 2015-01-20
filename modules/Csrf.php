<?php
namespace sbnc\modules;

use sbnc\Sbnc;

/**
 * Class Csrf
 *
 * Protects against Cross Site Request Forgery
 *
 * @package sbnc\modules
 */
class Csrf extends Module implements ModuleInterface
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
     * Set your custom error messages
     *
     * @var array Error messages
     */
    private $errors = [
        'invalid' => 'Invalid CSRF token',
        'none' => 'CSRF token does not exist'
    ];

    ######################################################################################
    ######################################################################################

    private $flash;

    protected function init()
    {
        if (Sbnc::utilExists('FlashMessages')) {
            $this->flash = Sbnc::getUtil('FlashMessages');
        } else {
            $this->enabled = false;
        }
    }

    public function check()
    {
        if (!$this->flash->exists('csrf', 'token') || !Sbnc::request('csrf')) {
            Sbnc::addError($this->errors['none']);
            Sbnc::log('spam-csrf', $this->errors['none']);
        } elseif (strcmp($this->flash->get('csrf', 'token'), Sbnc::request('csrf')) !== 0) {
            Sbnc::addError($this->errors['invalid']);
            Sbnc::log('spam-csrf', $this->errors['invalid']);
        }

    }

    public function after() {
        $token = $this->generateToken();
        Sbnc::addField('csrf', $token);
        $this->flash->flash('csrf', 'token', $token);
        session_regenerate_id();
    }

    private function generateToken()
    {
        if (function_exists("hash_algos") and in_array("sha512",hash_algos())) {
            $token = hash("sha512",mt_rand(0,mt_getrandmax()));
        } else {
            $token=' ';
            for ($i=0;$i<128;++$i) {
                $r=mt_rand(0,35);
                if ($r<26) {
                    $c=chr(ord('a')+$r);
                } else {
                    $c=chr(ord('0')+$r-26);
                }
                $token.=$c;
            }
        }
        return $token;
    }

}