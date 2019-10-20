<?php

namespace Zazama\DoubleOptIn\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextField;
use SilverStripe\UserForms\Model\EditableFormField\EditableEmailField;

class UserDefinedFormExtension extends Extension {
    private static $db = [
        'EnableDoubleOptIn' => 'Boolean(0)',
        'DoubleOptInSubject' => 'Varchar'
    ];

    private static $has_one = [
        'DoubleOptInField' => EditableEmailField::class
    ];

    public function updateFormOptions($options) {
        $options->add(CheckboxField::create('EnableDoubleOptIn', _t(__CLASS__.'.Enable', 'Enable Double-Opt-In')));
        $options->add(DropdownField::create('DoubleOptInFieldID', _t(__CLASS__.'.Field', 'Double-Opt-In E-Mail field'), EditableEmailField::get()->where(['ParentID' => $this->owner->ID]))->setEmptyString(''));
        $options->add(TextField::create('DoubleOptInSubject', _t(__CLASS__.'.Subject', 'Verification Subject')));
        return $options;
    }
}
