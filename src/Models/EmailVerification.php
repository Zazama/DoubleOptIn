<?php

namespace Zazama\DoubleOptIn\Models;

use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\RandomGenerator;
use SilverStripe\UserForms\Model\Submission\SubmittedForm;
use SilverStripe\View\ArrayData;
use Zazama\DoubleOptIn\Services\EmailSender;

class EmailVerification extends DataObject {

    use Configurable;
    /**
     * @config
     */
    private static $email_template = 'Zazama\\DoubleOptIn\\Email\\Email';
    private static $subject = 'Email verification';
    private static $url_segment = 'verify';

    private static $db = [
        'Email' => 'Varchar(255)',
        'Token' => 'Varchar(255)',
        'Storage' => 'Text',
        'Verified' => 'Boolean(0)'
    ];

    private static $has_one = [
        'SubmittedForm' => SubmittedForm::class
    ];

    private static $table_name = 'EmailVerification';

    public function init($email, $data = null) {
        $this->Email = $email;
        $this->Token = $this->generateToken();
        if($data) {
            $this->setStorage($data);
        }
        $this->write();
        $this->extend('updateInit', $this);
        return $this;
    }

    public function generateToken() {
        $generator = new RandomGenerator();
        $token = $generator->randomToken('sha512');
        $this->extend('updateGenerateToken', $token);
        return $token;
    }

    public function Link() {
        $link = Director::absoluteBaseURL() . $this->config()->get('url_segment') . '?token=' . $this->Token;
        $this->extend('updateLink', $link);
        return $link;
    }

    public function getSubject() {
        $subject = $this->config()->get('subject');
        $this->extend('updateSubject', $subject);
        return $subject;
    }

    public function send($subject = null) {
        if(!$subject) {
            $subject = $this->getSubject();
        }
        $data = new ArrayData([
            'Link' => $this->Link(),
            'Token' => $this->Token,
            'Storage' => $this->getStorage()
        ]);
        $sent = EmailSender::send($this->Email, $subject, $data->renderWith($this->config()->get('email_template')));
        return $sent;
    }

    public function getStorage() {
        if($this->dbObject('Storage')) {
            return unserialize($this->dbObject('Storage'));
        } else {
            return null;
        }
    }

    public function setStorage($data) {
        if($data) {
            $this->Storage = serialize($data);
            return true;
        } else {
            return false;
        }
    }
}
