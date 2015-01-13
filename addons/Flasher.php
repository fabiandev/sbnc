<?php
namespace Sbnc\Addons;

class Flasher extends Addon implements AddonInterface {

    // set explicit redirect for security reasons!!
    protected $options = [
        'redirect:error'    => [true, null],
        'redirect:success'  => [true, null]
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
            }
            if (!empty($this->master['errors']) && $this->options['redirect:error'][0] === true) {
                if (!$this->options['redirect:error'][1]) {
                    $this->master['utils']['FlashMessages']->flash('_sbnc', ['redirected' => true]);
                    header('Location: ' . $this->master['request']['url']);
                    exit;
                } else {
                    $this->master['utils']['FlashMessages']->flash('_sbnc', ['redirected' => true]);
                    header('Location: ' . $this->options['redirect:error'][1]);
                    exit;
                }
            } elseif (empty($this->master['errors']) && $this->options['redirect:success'][0] === true) {
                if (!$this->options['redirect:success'][1]) {
                    $this->master['utils']['FlashMessages']->flash('_sbnc', ['redirected' => true]);
                    header('Location: ' . $this->master['request']['url']);
                    exit;
                } else {
                    $this->master['utils']['FlashMessages']->flash('_sbnc', ['redirected' => true]);
                    header('Location: ' . $this->options['redirect:success'][1]);
                    exit;
                }
            }
        }
        $this->master['utils']['FlashMessages']->remove('_sbnc', 'redirected');
    }

    public function get_errors($safe = false) {
        if (!$this->enabled) return $this->master['errors'];
        $response =  $safe ? $this->master['utils']['FlashMessages']->get_safe('errors') :
                             $this->master['utils']['FlashMessages']->get('errors');
        return !empty($response) ? $response : $this->master['utils']['FlashMessages']->get_cache('errors');
    }

    public function count_errors() {
        if (!$this->enabled) return count($this->master['errors']);
        return $this->master['utils']['FlashMessages']->count('errors');
    }

    public function get_request($key, $safe = false) {
        if (!$this->enabled) return isset($this->master['request'][$key]) ? $this->master['request'][$key] : '';
        $response =  $safe ? $this->master['utils']['FlashMessages']->get_safe('request', $key) :
                             $this->master['utils']['FlashMessages']->get('request', $key);
        return !empty($response) ? $response : $this->master['utils']['FlashMessages']->get_cache('request', $key);
    }

}