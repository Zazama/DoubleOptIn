<?php

namespace Zazama\DoubleOptIn\Models;

use SilverStripe\Control\Email\Email;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\UserForms\Model\Submission\SubmittedForm;
use SilverStripe\UserForms\Model\Submission\SubmittedFormField;

class UserFormEmailToSend extends DataObject {
    private static $db = [
        'Email' => 'Text',
        'Recipient' => 'Text',
        'EmailData' => 'Text'
    ];

    private static $has_one = [
        'SubmittedForm' => SubmittedForm::class
    ];

    private static $table_name = 'UserFormEmailToSend';

    public function getData() {
        $data = [
            'email' => Email::create()
                ->setHTMLTemplate('email/SubmittedFormEmail')
                ->setPlainTemplate('email/SubmittedFormEmailPlain')
                ->setSwiftMessage(unserialize($this->Email)),
            'recipient' => unserialize($this->Recipient),
            'emailData' => unserialize($this->EmailData)
        ];
        $data['emailData']['Fields'] = [];
        foreach($data['emailData']['FieldIDs'] as $fieldid) {
            array_push($data['emailData']['Fields'], SubmittedFormField::get_by_id($fieldid));
        }
        foreach ($data['emailData'] as $key => $value) {
            $data['email']->addData($key, $value);
        }
        $data['email']->removeData('Fields');
        $fields = new ArrayList();
        foreach($data['emailData']['Fields'] as $field) {
            $fields->add($field);
        }
        $data['email']->addData('Fields', $fields);
        return $data;
    }

    public function setData($email, $recipient, $emailData) {
        $emailData['FieldIDs'] = [];
        foreach($emailData['Fields'] as $field) {
            array_push($emailData['FieldIDs'], $field->ID);
        }
        $emailData['Fields'] = null;
        $this->Email = serialize($email->getSwiftMessage());
        $this->Recipient = serialize($recipient);
        $this->EmailData = serialize($emailData);
        return true;
    }
}
