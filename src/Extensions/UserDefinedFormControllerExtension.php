<?php

namespace Zazama\DoubleOptIn\Extensions;

use SilverStripe\Core\Extension;
use Zazama\DoubleOptIn\Models\EmailDummy;
use Zazama\DoubleOptIn\Models\UserFormEmailToSend;

class UserDefinedFormControllerExtension extends Extension {
    public function updateEmail(&$email, $recipient, $emailData) {
        $page = $emailData['Fields'][0]->Parent()->Parent();
        if($page->EnableDoubleOptIn && $page->DoubleOptInFieldID) {
            $emailToSend = UserFormEmailToSend::create();
            $emailToSend->setData($email, $recipient, $emailData);
            $emailToSend->SubmittedFormID = $emailData['Fields'][0]->ParentID;
            $emailToSend->write();
            $email = EmailDummy::create();
        } else {
            return;
        }
    }
}
