<?php
namespace sbnc\addons;

use sbnc\Sbnc;

/**
 * Class Flasher
 *
 * Uses the util FlashMessages to flash errors and the request to the session.
 *
 * @package sbnc\addons
 */
class Flasher extends Addon implements AddonInterface
{

    ######################################################################################
    #########################           CONFIGURATION            #########################
    ######################################################################################

    /**
     * set if there should be a redirect on error/success
     * add a url to be redirected at the second value like this:
     * 'redirect:error' => [true, 'http://example.com/about/contact']
     * 'redirect:success' => [true, 'http://example.com/about/contact']
     *
     * set an explicit redirect for security reasons!!
     *
     * @var array Options
     */
    protected $options = [
        'redirect:error' => [true, null],
        'redirect:success' => [true, null]
    ];

    ######################################################################################
    ######################################################################################


    protected function init()
    {
        if (Sbnc::utilExists('FlashMessages')) {
            $this->enabled = Sbnc::util('FlashMessages')->isEnabled();
        }
    }

    public function after()
    {
        if (!$this->enabled) return;

        $errors = Sbnc::errors();
        $flash = Sbnc::util('FlashMessages');

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') === 0) {
            if (count(Sbnc::errors()) > 0) {
                $flash->flash('request', Sbnc::request());
                $flash->flash('errors', $errors);
            }
            if (!empty($errors) && $this->options['redirect:error'][0] === true) {
                if (!$this->options['redirect:error'][1]) {
                    $flash->flash('_sbnc', ['redirected' => true]);
                    if(!headers_sent()) {
                        header('Location: ' . Sbnc::request('url'));
                        exit;
                    } else {
                        $flash->flush();
                        Sbnc::printException(new \Exception('Headers have already been sent. Make sure to include sbnc before any other output'));
                    }

                } else {
                    $flash->flash('_sbnc', ['redirected' => true]);
                    if(!headers_sent()) {
                        header('Location: ' . $this->options['redirect:error'][1]);
                        exit;
                    } else {
                        $flash->flush();
                        Sbnc::printException(new \Exception('Headers have already been sent. Make sure to include sbnc before any other output'));
                    }
                }
            } elseif (empty($errors) && $this->options['redirect:success'][0] === true) {
                if (!$this->options['redirect:success'][1]) {
                    $flash->flash('_sbnc', ['redirected' => true]);
                    if(!headers_sent()) {
                        header('Location: ' . Sbnc::request('url'));
                        exit;
                    } else {
                        $flash->flush();
                        Sbnc::printException(new \Exception('Headers have already been sent. Make sure to include sbnc before any other output'));
                    }
                } else {
                    $flash->flash('_sbnc', ['redirected' => true]);
                    if(!headers_sent()) {
                        header('Location: ' . $this->options['redirect:success'][1]);
                        exit;
                    } else {
                        $flash->flush();
                        Sbnc::printException(new \Exception('Headers have already been sent. Make sure to include sbnc before any other output'));
                    }

                    exit;
                }
            }
        }
        // remove messages from session, retrieved from cache
        $flash->flush();
    }

    /**
     * returns array with all errors from session
     *
     * @return mixed
     */
    public function getErrors()
    {
        if (!$this->enabled) return Sbnc::errors();
        $response = Sbnc::util('FlashMessages')->get('errors');
        return !empty($response) ? $response : Sbnc::util('FlashMessages')->getCached('errors');
    }

    public function countErrors()
    {
        if (!$this->enabled) return count(Sbnc::errors());
        return Sbnc::util('FlashMessages')->count('errors');
    }

    public function getRequest($key)
    {
        if (!$this->enabled) return Sbnc::request($key) !== null ? Sbnc::request($key) : '';
        $response = Sbnc::util('FlashMessages')->get('request', $key);
        return !empty($response) ? $response : Sbnc::util('FlashMessages')->getCached('request', $key);
    }

    public function wasSubmitted()
    {
        if (Sbnc::util('FlashMessages')->exists('_sbnc', 'redirected')) {
            return true;
        }
        return false;
    }

}