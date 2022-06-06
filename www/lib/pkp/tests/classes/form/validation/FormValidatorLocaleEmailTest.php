<?php

/**
 * @file tests/classes/form/validation/FormValidatorLocaleEmailTest.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class FormValidatorLocaleEmailTest
 * @ingroup tests_classes_form_validation
 *
 * @see FormValidatorLocaleEmail
 *
 * @brief Test class for FormValidatorLocaleEmail.
 */

import('lib.pkp.tests.PKPTestCase');

use PKP\form\Form;
use PKP\form\validation\FormValidator;

class FormValidatorLocaleEmailTest extends PKPTestCase
{
    /**
     * @covers FormValidatorLocaleEmail
     * @covers FormValidatorLocale
     * @covers FormValidator
     */
    public function testIsValid()
    {
        $form = new Form('some template');

        $form->setData('testData', ['en_US' => 'some.address@gmail.com']);
        $validator = new \PKP\form\validation\FormValidatorLocaleEmail($form, 'testData', FormValidator::FORM_VALIDATOR_REQUIRED_VALUE, 'some.message.key');
        self::assertTrue($validator->isValid());

        $form->setData('testData', 'some.address@gmail.com');
        $validator = new \PKP\form\validation\FormValidatorLocaleEmail($form, 'testData', FormValidator::FORM_VALIDATOR_REQUIRED_VALUE, 'some.message.key');
        self::assertFalse($validator->isValid());

        $form->setData('testData', ['en_US' => 'anything else']);
        $validator = new \PKP\form\validation\FormValidatorLocaleEmail($form, 'testData', FormValidator::FORM_VALIDATOR_REQUIRED_VALUE, 'some.message.key');
        self::assertFalse($validator->isValid());
    }
}
