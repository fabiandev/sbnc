<?php
namespace sbnc\modules;

use sbnc\Sbnc;
use sbnc\utils\FlashMessages;

/**
 * Class RemoteHttpBlacklist
 *
 * Checks the client's IP against Project Honeypot's HTTP blacklist
 * http://www.projecthoneypot.org
 *
 * @package sbnc\modules
 */
class RemoteHttpBlacklist extends Module implements ModuleInterface
{

    ######################################################################################
    #########################           CONFIGURATION            #########################
    ######################################################################################

    /**
     * Sign up for a free account at http://www.projecthoneypot.org and
     * and create an API key for http:BL
     *
     * By default the API key will be loaded from the file honeypot.key
     * located at the same level as Sbnc.php
     *
     * @var
     */
    private $api_key;

    /**
     * Define your custom error messages.
     * You may use the %placeholders% from the example to get replaced
     *
     * @var array
     */
    private $errors = [
        'ip' => 'Your IP seems modified!',
        'spammer' => 'Spammer detected, that was last seen %days% day(s) ago, with around %num% messages per day, of type: %type%'
    ];

    ######################################################################################
    ######################################################################################


    private $ip; // 195.211.155.157
    private $flash;

    protected function init()
    {
        if (empty($this->api_key)) $this->api_key = file_get_contents('./honeypot.key');
        if (!empty($this->api_key)) $this->enabled = true;
        if (empty($this->ip)) $this->ip = $this->get_ip();
        $this->flash = new FlashMessages();
        $this->flash->set_namespace('sbnc_honeypot');
    }

    public function check()
    {
        if (!filter_var($this->ip, FILTER_VALIDATE_IP)) {
            $err = $this->errors['error'];
            Sbnc::add_error($err);
            return;
        }

        if ($this->flash->is_set($this->ip)) {
            $flash_data = $this->flash->get_safe($this->ip);
            if ($flash_data['spam'] === 1) $this->parse($flash_data);
            return;
        }

        $query = $this->api_key . '.' . implode('.', array_reverse(explode('.', $this->ip))) . '.dnsbl.httpbl.org';
        $response = gethostbyname($query);

        if (strcmp($query, $response) !== 0) {
            $response = explode('.', $response);

            if (strcmp($response[0], '127') === 0) {

                $data = [
                    'spam' => 1,
                    'type' => $response[0],
                    'activity' => $response[1],
                    'threat' => $response[2],
                    'meaning' => $response[3]
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
        $num = $data['threat'] < 26 ? 100 : $data['threat'] > 25 && $data['threat'] < 51 ? 10000 : 1000000;
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
        Sbnc::add_error($err);

        $this->flash->flash($this->ip, $data);
        Sbnc::util('LogMessages')->log('spam-http-blacklist', ['active ' . $days . ' day(s) ago', $num . ' messages/day', $type]);
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