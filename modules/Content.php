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
     *
     * - samecontent
     *      specify in how many fields there may be the exact same content
     *
     * @var array
     */
    private $options = [
        'maxlinks'    => 2,
        'mailwords'   => true,
        'spamwords'   => 3,
        'samecontent' => 2
    ];

    /**
     * Set your custom error messages
     *
     * @var array
     */
    private $errors = [
        'maxlinks'    => 'A maximum of %max% links (http://) are allowed on the entire form.',
        'mailwords'   => 'Mail injection detected. Do not use these words: bcc:, cc:, multipart, [url, Content-Type',
        'spamwords'   => 'A maximum of %max% blacklisted matches are allowed. You used: %words%',
        'samecontent' => 'More than %max% fields contain the exact same content'
    ];

    private $spamwords = [
        'д', 'и', 'ж', 'Ч', 'Б', '. ,', '? ,', '[url=', '[/url]',
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
        $this->options['maxlinks']++; // because request
    }


    public function check()
    {
        $request = implode(Sbnc::request());

        if (in_array('maxlinks', $this->options)) {
            if (preg_match_all("/<a|http:/i", $request, $out) > $this->options['maxlinks']) {
                $err = str_replace('%max%', $this->options['maxlinks'], $this->errors['maxlinks']);
                Sbnc::add_error($err);

                $log = 'Maximum of ' . $this->options['maxlinks'] . ' links reached' . $_SERVER['HTTP_REFERER'];
                Sbnc::util('LogMessages')->log('spam-content', $log);
            }
        }

        if (in_array('mailwords', $this->options)) {
            if (preg_match( "/bcc:|cc:|multipart|\[url|Content-Type:/i", $request)) {
                $err = $this->errors['mailwords'];
                Sbnc::add_error($err);
                Sbnc::util('LogMessages')->log('spam-content', 'Mail injection detected');
            }
        }
        if (in_array('spamwords', $this->options)) {

            $matches = [];

            foreach ($this->spamwords as $word) {
                if (strripos($request, $word)) {
                    array_push($matches, $word);
                }
            }

            if (count($matches) > $this->options['spamwords']) {
                $words = implode(', ', $matches);
                $err = str_replace(['%max%', '%words%'], [$this->options['maxlinks'], $words], $this->errors['spamwords']);
                Sbnc::add_error($err);
                $log = 'More than ' . $this->options['spamwords'] . ' spamwords found: ' . $words;
                Sbnc::util('LogMessages')->log('spam-content', $log);
            }
        }
    }

}