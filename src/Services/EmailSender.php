<?php

namespace Zazama\DoubleOptIn\Services;

use SilverStripe\Control\Email\Email;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Config\Configurable;

class EmailSender {

    use Configurable;

    /**
     * @config
     */
    private static $email_sender;

    public static function send($to, $subject, $body) {
        $from = Config::inst()->get("Zazama\\DoubleOptIn\\Services\\EmailSender", 'email_sender');
        if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
            user_error('Email sender not valid or not specified in config', E_USER_WARNING);
            return false;
        } else if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            user_error('Email receiver invalid', E_USER_NOTICE);
            return false;
        } else {
            $email = new Email($from, $to, $subject, $body);
            $email->send();
            return true;
        }
    }
}
