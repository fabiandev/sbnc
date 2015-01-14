<?php
namespace sbnc\utils;

use sbnc\Sbnc;

/**
 * Class LogMessages
 *
 * Used to log events to a file
 *
 * @package sbnc\utils
 */
class LogMessages extends Util implements UtilInterface
{

    ######################################################################################
    #########################           CONFIGURATION            #########################
    ######################################################################################

    /**
     * Log file
     *
     * @var array Options
     */
    private $options = [
        'file' => './sbnc.log'
    ];

    #######################################################################################
    #######################################################################################


    protected function init()
    {
        $this->enabled = true;
    }

    public function log($type, $data)
    {
        $content = '';
        $content .= strtoupper($type);
        $content .= '|' . date('d-m-Y G:i:s', time());
        if (is_array($data)) {
            foreach ($data as $value) {
                $content .= '|' . $value;
            }
        } else {
            $content .= '|' . $data;
        }

        $content .= '|' . $this->get_ip();
        $content .= '|' . $_SERVER['HTTP_USER_AGENT'];

        $content .= '|' . http_build_query(Sbnc::request());

        $content .= "\r\n";

        file_put_contents($this->options['file'], $content, FILE_APPEND);
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