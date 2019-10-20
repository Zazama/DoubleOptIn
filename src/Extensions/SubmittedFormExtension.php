<?php

namespace Zazama\DoubleOptIn\Extensions;

use SilverStripe\Control\Email\Email;
use SilverStripe\ORM\DataExtension;
use SilverStripe\UserForms\Model\Submission\SubmittedFormField;
use Zazama\DoubleOptIn\Models\EmailVerification;

class SubmittedFormExtension extends DataExtension {
    private static $db = [
        'SendVerification' => 'Boolean(0)'
    ];

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

    public function onBeforeWrite() {
        if(!$this->owner->isInDb()) {
            $this->owner->setField('SendVerification', '1');
        } else {
            $this->owner->setField('SendVerification', '0');
        }

        parent::onBeforeWrite();
    }

    public function onAfterWrite() {
        if(!$this->owner->getField('SendVerification')) {
            parent::onAfterWrite();
            return;
        }

        if($this->owner->Parent() && $this->owner->Parent()->EnableDoubleOptIn && $this->owner->Parent()->DoubleOptInFieldID) {
            $email = SubmittedFormField::get()->where(['Name' => $this->owner->Parent()->DoubleOptInField()->Name])->limit(1)[0];
            if(!$email) {
                return;
            }
            $emailVerification = EmailVerification::create();
            $emailVerification->init($email->Value);
            $this->owner->EmailVerificationID = $emailVerification->ID;
            $emailVerification->SubmittedFormID = $this->owner->ID;
            $emailVerification->send($this->owner->Parent()->DoubleOptInSubject);
            $emailVerification->write();
            $this->owner->write();
        }
        parent::onAfterWrite();
    }
}
