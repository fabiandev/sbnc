<?php
namespace sbnc\utils;

use sbnc\Sbnc;
use sbnc\core\Util;

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
     * May be disabled if any inconsistencies occur
     *
     * @var bool Enable or disable module
     */
    protected $enabled = true;

    /**
     * Log file
     *
     * @var string File
     */
    private $log_file = './sbnc.log';

    #######################################################################################
    #######################################################################################


    protected function init()
    {

    }

    public function log($type, $data)
    {
        if (!$this->isEnabled()) return;
        if (!$this->checkFile()) return;

        $content = '';
        $content .= strtoupper($type);
        $content .= '|' . date('d-m-Y G:i:s', time());
        $content .= '|' . $data;

        $content .= '|' . $this->getIp();
        $content .= '|' . $_SERVER['HTTP_USER_AGENT'];

        $content .= '|' . http_build_query(Sbnc::request());

        $content .= "\r\n";

        file_put_contents($this->log_file, $content, FILE_APPEND);
    }

    public function getLog($reverse = true)
    {
        if (!$this->checkFile()) return false;

        $str = file_get_contents($this->log_file);
        $arr = explode("\n", $str);
        for ($i = 0; $i < count($arr); $i++) {
            $arr[$i] = explode('|', $arr[$i]);
            if (isset($arr[$i][5])) {
                parse_str($arr[$i][5], $arr[$i][5]);
            }

            $keys = ['type', 'time', 'message', 'ip', 'client', 'request'];
            $values = array_values($arr[$i]);

            if (count($keys) != count($values)) continue;

            $arr[$i] = array_combine($keys, $values);
        }
        array_pop($arr);
        return $reverse ? array_reverse($arr): $arr;
    }

    private function checkFile() {
        if (!file_exists($this->log_file)) {
            fclose(fopen($this->log_file, 'w'));
        }
        if (!is_writeable($this->log_file)) {
            return false;
        }
        return true;
    }

    protected function getIp()
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