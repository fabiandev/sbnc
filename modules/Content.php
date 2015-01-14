<?php
namespace sbnc\modules;

use sbnc\Sbnc;

/**
 * Class Content
 * @package sbnc\modules
 */
class Content extends Module implements ModuleInterface
{

    ######################################################################################
    #########################           CONFIGURATION            #########################
    ######################################################################################

    /**
     * Module options
     *
     * - maxlinks
     *      define how often you allow the http:// or <a in your form
     *
     * - mailwords
     *      gives an error if these string are used in the form:
     *      bcc: cc: multipart [url Content-Type:
     *
     * - spamwords
     *      allow a maximum of words from the spamwords list in your form
     *      multiple words count as one match e.g. "buy viagra today at viagrastore.com"
     *      gets a score of 1
     *      to change this behavior set the second value at the spamwords option to true
     *
     * - samecontent
     *      specify in how many fields there may be the exact same content
     *
     * @var array Options
     */
    private $options = [
        'maxlinks' => 2,
        'mailwords' => true,
        'spamwords' => [3, false]
    ];

    /**
     * Set your custom error messages
     *
     * @var array Error messages
     */
    private $errors = [
        'maxlinks' => 'A maximum of %max% links (http://) are allowed on the entire form.',
        'mailwords' => 'Mail injection detected. Do not use these words: bcc:, cc:, multipart, [url, Content-Type',
        'spamwords' => 'A maximum of %max% blacklisted matches are allowed. Matches: %words%'
    ];

    /**
     * List of spam words
     *
     * @var array Collection of spam words
     */
    private $spamwords = [
        'д', 'и', 'ж', 'Ч', 'Б', '\. ,', '\? ,', '\[url=', '\[/url\]',
        '-online', '4u', 'aceteminophen', 'adderall', 'adipex', 'advicer', 'ambien', 'anime', 'ass', 'augmentation',
        'baccarat', 'baccarrat', 'bdsm', 'bitch', 'blackjack', 'bllogspot', 'booker', 'breast', 'byob',
        'car-rental-e-site', 'car-rentals-e-site', 'carisoprodol', 'casino', 'casinos', 'cephalaxin', 'chatroom',
        'cialis', 'citalopram', 'clomid', 'cock', 'coolcoolhu', 'coolhu', 'credit card', 'credit-card-debt',
        'credit-report-4u', 'cwas', 'cyclen', 'cyclobenzaprine', 'cymbalta', 'dating', 'dating-e-site', 'day-trading',
        'debt', 'debt-consolidation', 'debt-consolidation-consultant', 'dick', 'discreetordering', 'doxycycline',
        'duty-free', 'dutyfree', 'enhancement', 'ephedra', 'equityloans', 'facial', 'femdom', 'fetish', 'finance',
        'fioricet', 'flowers', 'flowers-leading-site', 'freenet', 'freenet-shopping', 'fuck', 'fucking', 'gambling',
        'gambling-', 'gay', 'gdf', 'gds', 'hair-loss', 'health-insurancedeals-4u', 'holdem', 'holdempoker',
        'holdemsoftware', 'holdemtexasturbowilson', 'homeequityloans', 'homefinance', 'hotel', 'hotel-dealse-site',
        'hotele-site', 'hotelse-site', 'hqtube', 'hydrocodone', 'incest', 'insurance', 'insurance-quotesdeals-4u',
        'insurancedeals-4u', 'jrcreations', 'lesbian', 'levitra', 'lexapro', 'lipitor', 'loan', 'lorazepam',
        'lunestra', 'macinstruct', 'male', 'mortgage', 'mortgage-4-u', 'mortgagequotes', 'meridia', 'myspace',
        'naked', 'nude', 'online-gambling', 'onlinegambling-4u', 'ottawavalleyag', 'ownsthis', 'oxycodone', 'oxycontin',
        'palm-texas-holdem-game', 'paxil', 'payday', 'penis', 'percocet', 'pharmacy', 'phentermine', 'pills', 'poker',
        'poker-chip', 'porn', 'porno', 'poze', 'propecia', 'pussy', 'rental', 'rental-car-e-site', 'ringtone',
        'ringtones', 'roulette', 'sex', 'shemale', 'shit', 'shoes', 'slot-machine', 'soma', 'texas holdem',
        'texas-holdem', 'thorcarlson', 'thx', 'tits', 'titties', 'top-e-site', 'top-site', 'tramadol', 'trading',
        'trim-spa', 'ultram', 'valeofglamorganconservatives', 'valium', 'valtrex', 'viagra', 'vicodin', 'vicoprofen',
        'vioxx', 'visa', 'xanax', 'xenical', 'youtube', 'zolus'
    ];

    ######################################################################################
    ######################################################################################


    protected function init()
    {
        $this->enabled = true;
        $this->options['maxlinks']++;
    }


    public function check()
    {
        $request = implode(Sbnc::request());

        if (array_key_exists('maxlinks', $this->options)) {
            if (preg_match_all("/<a|http:/i", $request, $out) > $this->options['maxlinks']) {
                $err = str_replace('%max%', $this->options['maxlinks'], $this->errors['maxlinks']);
                Sbnc::add_error($err);

                $log = 'Maximum of ' . $this->options['maxlinks'] . ' links reached' . $_SERVER['HTTP_REFERER'];
                Sbnc::util('LogMessages')->log('spam-content', $log);
            }
        }

        if (array_key_exists('mailwords', $this->options)) {
            if (preg_match("/bcc:|cc:|multipart|\[url|Content-Type:/i", $request)) {
                $err = $this->errors['mailwords'];
                Sbnc::add_error($err);
                Sbnc::util('LogMessages')->log('spam-content', 'Mail injection detected');
            }
        }
        if (array_key_exists('spamwords', $this->options)) {

            $names = implode('|', $this->spamwords);
            $regex = '#[-+]?(' .  $names . ')#';
            preg_match_all($regex, $request, $all_matches);

            if (!$this->options['spamwords'][1]) {
                $matches = isset($all_matches[0]) ? array_unique($all_matches[0]) : [];
            } else {
                $matches = isset($all_matches[0]) ? $all_matches[0] : [];
            }

            if (count($matches) > $this->options['spamwords'][0]) {
                $words = implode(', ', $matches);
                $err = str_replace(['%max%', '%words%'], [$this->options['spamwords'][0], $words], $this->errors['spamwords']);
                Sbnc::add_error($err);
                $log = 'More than ' . $this->options['spamwords'][0] . ' spamwords found: ' . $words;
                Sbnc::util('LogMessages')->log('spam-content', $log);
            }
        }
    }

}