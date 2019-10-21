<?php

namespace Zazama\DoubleOptIn\Extensions;

use SilverStripe\Control\Email\Email;
use SilverStripe\ORM\DataExtension;
use Zazama\DoubleOptIn\Models\EmailVerification;

class SubmittedFormExtension extends DataExtension {
    private static $has_one = [
        'EmailVerification' => EmailVerification::class
    ];

    public function canView($member = null) {
        if($this->owner->Parent()) {
            if($this->owner->Parent()->EnableDoubleOptIn) {
                if($this->owner->EmailVerification()->Verified || !$this->owner->Parent()->DoubleOptInFieldID) {
                    return $this->owner->Parent()->canView($member);
                } else {
                    return false;
                }
            } else {
                return $this->owner->Parent()->canView($member);
            }
        } else {
            return parent::canView($member);
        }
    }
}
