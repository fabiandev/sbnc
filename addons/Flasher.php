<?php
namespace Sbnc\Addons;

class Flasher extends Addon implements AddonInterface {


    protected $enabled = false;

    // set explicit redirect for security reasons!!
    protected $options = [
        'redirect:error'    => [true, null],
        'redirect:success'  => [true, null],
        'message:success'   => 'No Spam, Yay!'
    ];

    protected function init() {
        $this->enabled = $this->master['utils']['FlashMessages']->is_enabled();
    }

    public function after() {
        if (!$this->enabled) return;
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') === 0) {
            if (count($this->master['errors']) > 0) {
                $this->master['utils']['FlashMessages']->flash('request', $this->master['request']);
                $this->master['utils']['FlashMessages']->flash('errors', $this->master['errors']);
            } else {
                $this->master['utils']['FlashMessages']->flash('messages', $this->options['message:success'], 'success');
            }
            if (!empty($this->master['errors']) && $this->options['redirect:error'][0] === true) {
                if (!$this->options['redirect:error'][1]) {
                    header('Location: ' . $this->master['request']['url']);
                    exit;
                } else {
                    header('Location: ' . $this->options['redirect:error'][1]);
                    exit;
                }
            } elseif (empty($this->master['errors']) && $this->options['redirect:success'][0] === true) {
                if (!$this->options['redirect:success'][1]) {
                    header('Location: ' . $this->master['request']['url']);
                    exit;
                } else {
                    header('Location: ' . $this->options['redirect:success'][1]);
                    exit;
                }
            }
        }
    }

    public function get_errors() {
        if (!$this->enabled) return $this->master['errors'];
        return $this->master['utils']['FlashMessages']->get('errors');
    }

    public function count_errors() {
        if (!$this->enabled) return count($this->master['errors']);
        return $this->master['utils']['FlashMessages']->count('errors');
    }

    public function get_request($key) {
        if (!$this->enabled) return isset($this->master['request'][$key]) ? $this->master['request'][$key] : '';
        //echo $this->master['utils']['FlashMessages']->get('request', $key);
        return $this->master['utils']['FlashMessages']->get('request', $key);
    }

}