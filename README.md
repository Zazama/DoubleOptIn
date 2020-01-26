# DoubleOptIn for Silverstripe

Important: This module is pretty new so expect it to have some bugs.

## Introduction

DoubleOptIn extends Silverstripe and Userforms to implement a Double-Opt-In solution that is often required by new GDPR law.
It will add a checkbox to Userforms to send a verification email before the submission/email is viewable/sent.

## Requirements

* silverstripe/framework ^4.0
* silverstripe/admin ^1.0
* silverstripe/userforms ^5.0

This module was only tested on the newest 4.4.

## Installation

```
composer require zazama/doubleoptin
```

### YAML configuration

```yaml
# set a route for verification links
SilverStripe\Control\Director:
  rules:
    'verify': Zazama\DoubleOptIn\Controllers\VerificationController
Zazama\DoubleOptIn\Models\EmailVerification:
  url_segment: 'verify'
  # subject is ignored with Userforms, because that's set in admin frontend.
  subject: 'Email Verification'
# set sender for the verification email
Zazama\DoubleOptIn\Services\EmailSender:
  email_sender: 'example@yourdomain.com'
```

### Userforms settings

* To enable DoubleOptIn in a Userforms page, go to the Configuration tab and click "Enable Double-Opt-In"
* Choose the Double-Opt-In E-Mail field (required, too)
* Enter your verification email subject.

### Usage with normal forms

```php
    use Zazama\DoubleOptIn\Models\EmailVerification;

    public function handleFormVerification($data, $subject) {
        $email = $data['email'];
        $emailVerification = EmailVerification::create();
        $emailVerification->init($email);
        $emailVerification->send($subject);
        $emailVerification->write();
    }
    
    public function tokenChecks() {
        EmailVerification::IsSuccess($token); //bool
        EmailVerification::IsAlreadyVerified($token); //bool
        EmailVerification::IsBadToken($token); //bool
        EmailVerification::TokenType($token); //string
    }
```

### Templates

The following templates can be overriden:

* Zazama\DoubleOptIn\Layout\Verification_Success
* Zazama\DoubleOptIn\Layout\Verification_BadToken
* Zazama\DoubleOptIn\Layout\Verification_AlreadyVerified
* Zazama\DoubleOptIn\Email\Email

If you just want to change the text, you can change the language strings.

### Extension hooks

There are extension hooks provided to use the Verification controller.

```php
class x extends Zazama\DoubleOptIn\Controllers\VerificationController {
  # called when successfully verified
  public function updateSuccess($token) {}

  # called when verification token is wrong / not provided
  public function updateBadToken() {}

  # called when verification token is already verified
  public function updateAlreadyVerified() {}
}

class y extends Zazama\DoubleOptIn\Models\EmailVerification {
  # change generated token
  public function updateGenerateToken($token) {}

  # change link
  public function updateLink($link) {}

  # change subject
  public function updateSubject($subject) {}
}
```
