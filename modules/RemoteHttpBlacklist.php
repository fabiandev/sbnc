<?php
namespace sbnc\modules;

use sbnc\Sbnc;

/**
 * Class RemoteHttpBlacklist
 *
 * Checks the client's IP against Project Honeypot's HTTP blacklist
 * Note: only supports IPv4 at this time.
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
     * May be disabled if any inconsistencies occur
     *
     * @var bool Enable or disable module
     */
    protected $enabled = true;

    /**
     * Sign up for a free account at http://www.projecthoneypot.org and
     * and create an API key for http:BL
     *
     * By default the API key will be loaded from ta file.
     *
     * @var string File containing the API Key
     */
    private $api_key_file = './honeypot.key';

    /**
     * You may also place your API Key here.
     * By doing so, it won't be loaded from the specified file.
     *
     * private $api_key = 'YOUR_API_KEY';
     *
     * For security reasons, this is not recommended though. Consider placing the file containing
     * the key outside of the public directory, or add the key as an environment variable.
     * You can get it from an environment variable like this:
     *
     * private $api_key = getenv('NAME_OF_VARIABLE');
     *
     * @var string API Key
     */
    private $api_key;

    /**
     * Define your custom error messages.
     * You may use the %placeholders% from the example to get replaced
     *
     * @var array Error messages
     */
    private $errors = [
        'ip' => 'Your IP seems modified!',
        'spammer' => '%type% detected, last seen %days% day(s) ago, with around %num% messages per day.'
    ];

    ######################################################################################
    ######################################################################################


    private $ip; // tests: http://www.projecthoneypot.org/httpbl_api.php e.g. 127.1.1.3
    private $flash;
    private $cached = false;

    protected function init()
    {
        if (empty($this->api_key) && !empty($this->api_key_file)) {
            if (!is_readable($this->api_key_file)) return;
            $this->api_key = file_get_contents($this->api_key_file);
        }
        if (!empty($this->api_key)) $this->enabled = true;
        if (empty($this->ip)) $this->ip = $this->get_ip();

        if (Sbnc::utilExists('FlashMessages')) {
            $flash = Sbnc::getUtil('FlashMessages');
            if ($flash->isEnabled()) {
                $this->flash = $flash;
                $this->cached = true;
            }
        }
    }

    public function check()
    {
        if (!filter_var($this->ip, FILTER_VALIDATE_IP)) {
            $err = $this->errors['error'];
            Sbnc::addError($err);
            return;
        }

        if ($this->cached && $this->flash->exists('httpBL', $this->ip)) {
            $flash_data = $this->flash->getSafe('httpBL', $this->ip);
            if ($flash_data['spam'] === 1) {
                $this->parse($flash_data);
            }
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
            if ($this->cached) $this->flash->flash('httpBL', $this->ip, ['spam' => 0]);
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
        Sbnc::addError($err);

        if ($this->cached) $this->flash->flash('httpBL', $this->ip, $data);
        Sbnc::log('spam-http-blacklist', $type . ', ' . 'active ' . $days . ' day(s) ago' . ', ' . $num . ' messages/day');
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