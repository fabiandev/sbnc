<?php
namespace sbnc\addons;

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
        // remove messages from session, retrieved from cache
        $this->master['utils']['FlashMessages']->flush();
    }

    public function get_errors() {
        if (!$this->enabled) return $this->master['errors'];
        $response = $this->master['utils']['FlashMessages']->get('errors');
        return !empty($response) ? $response : $this->master['utils']['FlashMessages']->get_cache('errors');
    }

    public function count_errors() {
        if (!$this->enabled) return count($this->master['errors']);
        return $this->master['utils']['FlashMessages']->count('errors');
    }

    public function get_request($key) {
        if (!$this->enabled) return isset($this->master['request'][$key]) ? $this->master['request'][$key] : '';
        $response = $this->master['utils']['FlashMessages']->get('request', $key);
        return !empty($response) ? $response : $this->master['utils']['FlashMessages']->get_cache('request', $key);
    }

    public function was_submitted() {
        if ($this->master['utils']['FlashMessages']->is_set('_sbnc', 'redirected')) {
            return true;
        }
        return false;
    }

}