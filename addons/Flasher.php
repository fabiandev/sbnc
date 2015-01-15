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
     *
     * 'redirect:error' => 'http://example.com/about/contact'
     * 'redirect:success' => 'http://example.com/about/contact'
     *
     * set to false to disable redirecting or leave empty to redirect back to
     * the requesting url.
     *
     * SET AN EXPLICIT REDIRECT FOR SECURITY REASONS!
     *
     * @var array Options
     */
    protected $options = [
        'redirect:error' => '',
        'redirect:success' => ''
    ];

    ######################################################################################
    ######################################################################################

    private $flash;

    protected function init()
    {
        if (Sbnc::utilExists('FlashMessages')) {
            $this->enabled = Sbnc::util('FlashMessages')->isEnabled();
            $this->flash = Sbnc::util('FlashMessages');
        }
    }

    public function __destruct() {
        $errors = Sbnc::errors();
        if ((!empty($errors) && $this->options['redirect:error'] === false) ||
            (empty($errors) && $this->options['redirect:success'] === false))
        {
            $this->flash->flush();
        }
    }

    public function after()
    {
        if (!$this->enabled) return;

        $errors = Sbnc::errors();

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') === 0) {
            if (count(Sbnc::errors()) > 0) {
                $this->flash->flash('request', Sbnc::request());
                $this->flash->flash('errors', $errors);
            }
            if (!empty($errors) && $this->options['redirect:error'] !== false) {
                if (empty($this->options['redirect:error'])) {
                    if(!headers_sent()) {
                        $this->flash->flash('_sbnc', ['submitted' => true]);
                        header('Location: ' . Sbnc::request('url'));
                        exit;
                    } else {
                        $this->flash->flush();
                        Sbnc::printException(new \Exception('Headers have already been sent. Make sure to include sbnc before any other output'));
                    }

                } else {
                    $this->flash->flash('_sbnc', ['submitted' => true]);
                    if(!headers_sent()) {
                        header('Location: ' . $this->options['redirect:error']);
                        exit;
                    } else {
                        $this->flash->flush();
                        Sbnc::printException(new \Exception('Headers have already been sent. Make sure to include sbnc before any other output'));
                    }
                }
            } elseif (empty($errors) && $this->options['redirect:success'] !== false) {
                if (empty($this->options['redirect:success'])) {
                    if(!headers_sent()) {
                        $this->flash->flash('_sbnc', ['submitted' => true]);
                        header('Location: ' . Sbnc::request('url'));
                        exit;
                    } else {
                        $this->flash->flush();
                        Sbnc::printException(new \Exception('Headers have already been sent. Make sure to include sbnc before any other output'));
                    }
                } else {
                    if(!headers_sent()) {
                        $this->flash->flash('_sbnc', ['submitted' => true]);
                        header('Location: ' . $this->options['redirect:success']);
                        exit;
                    } else {
                        $this->flash->flush();
                        Sbnc::printException(new \Exception('Headers have already been sent. Make sure to include sbnc before any other output'));
                    }
                    exit;
                }
            } else {
                $this->flash->flash('_sbnc', ['submitted' => true]);
                return;
            }
        }
        // remove messages from session, retrieved from cache
        $this->flash->flush();
    }

    /**
     * returns array with all errors from session
     *
     * @return mixed
     */
    public function getErrors()
    {
        if (!$this->enabled) return Sbnc::errors();
        $response = $this->flash->get('errors');
        return !empty($response) ? $response : Sbnc::util('FlashMessages')->getCached('errors');
    }

    public function countErrors()
    {
        if (!$this->enabled) return count(Sbnc::errors());
        return $this->flash->count('errors');
    }

    public function getRequest($key)
    {
        if (!$this->enabled) return Sbnc::request($key) !== null ? Sbnc::request($key) : '';
        $response = $this->flash->get('request', $key);
        return !empty($response) ? $response : $this->flash->getCached('request', $key);
    }

    public function wasSubmitted()
    {
        if (!$this->enabled) return strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') === 0;
        if ($this->flash->exists('_sbnc', 'submitted')) {
            return true;
        }
        return false;
    }

}