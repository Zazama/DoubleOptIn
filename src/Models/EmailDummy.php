<?php

namespace Zazama\DoubleOptIn\Models;

use SilverStripe\Control\Email\Email;

class EmailDummy extends Email {
    public function send() {
        return true;
    }

    public function sendPlain() {
        return true;
    }
}
