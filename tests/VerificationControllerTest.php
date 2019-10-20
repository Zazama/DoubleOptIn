<?php

namespace Zazama\DoubleOptIn\Tests;

use Page;
use Zazama\DoubleOptIn\Controllers\VerificationController;
use Zazama\DoubleOptIn\Models\EmailVerification;
use Zazama\DoubleOptIn\Tests\Src\VerificationTestPage;
use SilverStripe\Dev\FunctionalTest;

class VerificationControllerTest extends FunctionalTest {
    protected static $fixture_file = 'VerificationControllerTest.yml';

    protected function setUp()
    {
        parent::setUp();
    }

    public function testVerificationControllerIndex() {
        $page = $this->objFromFixture(VerificationTestPage::class, 'test');
        $page->publishSingle();
        $this->assertContains('Your token is invalid. Please try clicking the link again.', $this->get('verify-test')->getBody());
        $this->assertContains('Your token is invalid. Please try clicking the link again.', $this->get('verify-test?token=SKdhakojbwedbklas')->getBody());

        $verified = $this->objFromFixture(EmailVerification::class, 'verified');
        $verified->write();
        $this->assertContains('Your E-Mail address was already verified.', $this->get('verify-test?token=' . $verified->Token)->getBody());

        $unverified = $this->objFromFixture(EmailVerification::class, 'unverified');
        $unverified->write();
        $this->assertContains('You have successfully verified your E-Mail address.', $this->get('verify-test?token=' . $unverified->Token)->getBody());
    }
}
