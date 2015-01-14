<?php
namespace sbnc\modules;

use sbnc\utils\FlashMessages;

class RemoteHttpBlacklist extends Module implements ModuleInterface
{

    // sign up for a free account on http://www.projecthoneypot.org and
    // and create an api key for http:BL
    private $api_key;

    private $ip; // 195.211.155.157

    private $flash;

    private $errors = [
        'ip'      => 'Spam! Your IP seems modified!',
        'spammer' => 'Spammer detected, that was last seen %days% day(s) ago, with around %num% messages per day, of type: %type%'
    ];

    protected function init()
    {
        $this->api_key = file_get_contents('./honeypot.key');
        if (!empty($this->api_key)) $this->enabled = true;
        if (empty($this->ip)) $this->ip = $this->get_ip();
        $this->flash = new FlashMessages();
        $this->flash->set_namespace('sbnc_honeypot');
    }

    public function check()
    {
        if (!filter_var($this->ip, FILTER_VALIDATE_IP)) {
            $err = $this->errors['error'];
            array_push($this->master['errors'], $err);
            return;
        }

        if ($this->flash->is_set($this->ip)) {
            $flash_data = $this->flash->get_safe($this->ip);
            if($flash_data['spam'] === 1) $this->parse($flash_data);
            return;
        }

        $query = $this->api_key . '.' . implode('.', array_reverse(explode('.', $this->ip))) . '.dnsbl.httpbl.org';
        $response = gethostbyname($query);

        if (strcmp($query, $response) !== 0) {
            $response = explode('.', $response);

            if (strcmp($response[0], '127') === 0) {

                $data = [
                    'spam'     => 1,
                    'type'     => $response[0],
                    'activity' => $response[1],
                    'threat'   => $response[2],
                    'meaning'  => $response[3]
                ];

                $this->parse($data);

            }

        } else {
            $this->flash->flash($this->ip, ['spam' => 0]);
        }

    }

    public function parse($data)
    {
        $days = $data['activity'];
        $num  = $data['threat'] < 26 ? 100 : $data['threat'] > 25 && $data['threat'] < 51 ? 10000 : 1000000;
        $type = '';

        switch ($data['meaning']) {
            case 0:
                $type = 'Search Engine';
                break;
            case 1:
                $type = 'Suspicious';
                break;
            case 2:
                $type = 'Harvester';
                break;
            case 3:
                $type = 'Comment Spammer';
                break;
            case 4:
                $type = 'Suspicious and Comment Spammer';
                break;
            case 5:
                $type = 'Harvester and Comment Spammer';
                break;
            case 6:
                $type = 'Suspicious, Harvester and Comment Spammer';
                break;
        }

        $err = str_replace(['%days%', '%num%', '%type%'], [$days, number_format($num, 0, '.', ','), $type], $this->errors['spammer']);
        array_push($this->master['errors'], $err);

        $this->flash->flash($this->ip, $data);
        $this->master['utils']['LogMessages']->log('spam-http-blacklist', ['active '.$days.' day(s) ago', $num . ' messages/day', $type]);
    }

    protected function get_ip()
    {
        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip;
    }

}