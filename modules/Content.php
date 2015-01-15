<?php
namespace sbnc\modules;

use sbnc\Sbnc;

/**
 * Class Content
 *
 * Can count links, spam words and block mail injections by keywords
 *
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
     * - spam_words
     *      allow a maximum of words from the spam_words list in your form
     *      multiple words count as one match e.g. "buy viagra today at viagrastore.com"
     *      gets a score of 1
     *      to change this behavior set the second value at the spam_words option to true
     *
     * - samecontent
     *      specify in how many fields there may be the exact same content
     *
     * @var array Options
     */
    private $options = [
        'max_links' => 2,
        'mail_words' => true,
        'spam_words' => [3, false]
    ];

    /**
     * Set your custom error messages
     *
     * @var array Error messages
     */
    private $errors = [
        'max_links' => 'A maximum of %max% links (http://) are allowed on the entire form.',
        'mail_words' => 'Mail injection detected. Do not use these words: bcc:, cc:, multipart, [url, Content-Type',
        'spam_words' => 'A maximum of %max% blacklisted matches are allowed. Matches: %words%'
    ];

    /**
     * List of spam words
     *
     * @var array Collection of spam words
     */
    private $spam_words = [
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
        $this->options['max_links']++; // count up because request includes form url
    }


    public function check()
    {
        $request = implode(Sbnc::request());

        if (array_key_exists('max_links', $this->options)) {
            if (preg_match_all("/<a|http:/i", $request, $out) > $this->options['max_links']) {
                $err = str_replace('%max%', $this->options['max_links'], $this->errors['max_links']);
                Sbnc::add_error($err);

                $log = 'Maximum of ' . $this->options['max_links'] . ' links reached' . $_SERVER['HTTP_REFERER'];
                Sbnc::util('LogMessages')->log('spam-content', $log);
            }
        }

        if (array_key_exists('mail_words', $this->options)) {
            if (preg_match("/bcc:|cc:|multipart|\[url|Content-Type:/i", $request)) {
                $err = $this->errors['mailwords'];
                Sbnc::add_error($err);
                Sbnc::util('LogMessages')->log('spam-content', 'Mail injection detected');
            }
        }
        if (array_key_exists('spam_words', $this->options)) {

            $names = implode('|', $this->spam_words);
            $regex = '#[-+]?(' .  $names . ')#';
            preg_match_all($regex, $request, $all_matches);

            if (!$this->options['spam_words'][1]) {
                $matches = isset($all_matches[0]) ? array_unique($all_matches[0]) : [];
            } else {
                $matches = isset($all_matches[0]) ? $all_matches[0] : [];
            }

            if (count($matches) > $this->options['spam_words'][0]) {
                $words = implode(', ', $matches);
                $err = str_replace(['%max%', '%words%'], [$this->options['spam_words'][0], $words], $this->errors['spam_words']);
                Sbnc::addError($err);
                $log = 'More than ' . $this->options['spam_words'][0] . ' spam_words found: ' . $words;
                Sbnc::util('LogMessages')->log('spam-content', $log);
            }
        }
    }

}