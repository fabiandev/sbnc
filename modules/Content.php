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
     * May be disabled if any inconsistencies occur
     *
     * @var bool Enable or disable module
     */
    protected $enabled = true;

    /**
     * Module options
     *
     * - maxlinks
     *      check how often you allow the http:// or <a in your form
     *
     * - mailwords
     *      gives an error if these string are used in the form:
     *      bcc: cc: multipart [url Content-Type:
     *
     * - spam_words
     *      allow a maximum of words from the spam_words list in your form
     *      multiple words count as one match e.g. "buy viagra today at viagrastore.com"
     *      gets a score of 1
     *
     * @var array Options
     */
    private $use = ['max_links', 'mail_words', 'spam_words'];

    /**
     * Set options the used checks
     *
     * @var array
     */
    private $options = [
        'max_links' => [
            'max' => 2
        ],
        'spam_words' => [
            'max' => 2,
            'count_duplicates' => false
        ]
    ];

    /**
     * Set your custom error messages
     *
     * @var array Error messages
     */
    private $errors = [
        'max_links' => 'A maximum of %max% urls are allowed on the entire form.',
        'mail_words' => 'Mail injection detected. Do not use these words: bcc:, cc:, multipart, [url, Content-Type',
        'spam_words' => 'A maximum of %max% blacklisted matches are allowed, %matches% used: %words%'
    ];

    /**
     * List of spam words
     *
     * @var array Collection of spam words
     */
    private $spam_words = [
        'д', 'и', 'ж', 'Ч', 'Б', '\. ,', '\? ,',
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
        if (isset($this->options['max_links']['max'])) {
            $this->options['max_links']['max']++;
        }
    }


    public function check()
    {
        $request = implode(Sbnc::request());

        if (in_array('max_links', $this->use)) {
            if (preg_match_all("#http:#i", $request, $out) > $this->options['max_links']['max']) {
                $err = str_replace('%max%', $this->options['max_links']['max'], $this->errors['max_links']);
                Sbnc::addError($err);

                $log = 'Maximum of ' . $this->options['max_links']['max'] . ' links reached.';
                Sbnc::log('spam-content', $log);
            }
        }

        if (in_array('mail_words', $this->use)) {
            if (preg_match("/bcc:|cc:|multipart|Content-Type:/i", $request)) {
                $err = $this->errors['mail_words'];
                Sbnc::addError($err);
                Sbnc::log('spam-content', 'Mail injection detected');
            }
        }
        if (in_array('spam_words', $this->use)) {

            $names = implode('|', $this->spam_words);
            $regex = '#[-+]?(' . $names . ')#';
            preg_match_all($regex, $request, $all_matches);

            if (!$this->options['spam_words']['count_duplicates']) {
                $matches = isset($all_matches[0]) ? array_unique($all_matches[0]) : [];
            } else {
                $matches = isset($all_matches[0]) ? $all_matches[0] : [];
            }

            $num_matches = count($matches);

            if ($num_matches > $this->options['spam_words']['max']) {
                $words = implode(', ', $matches);
                $err = str_replace(
                    ['%max%', '%words%', '%matches%'],
                    [$this->options['spam_words']['max'], $words, $num_matches],
                    $this->errors['spam_words']
                );
                Sbnc::addError($err);
                $log = 'More than ' . $this->options['spam_words']['max'] . ' spam words found: ' . $words;
                Sbnc::log('spam-content', $log);
            }
        }
    }

}