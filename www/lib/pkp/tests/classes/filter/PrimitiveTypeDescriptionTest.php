<?php

/**
 * @file tests/classes/filter/PrimitiveTypeDescriptionTest.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PrimitiveTypeDescriptionTest
 * @ingroup tests_classes_filter
 *
 * @see PrimitiveTypeDescription
 *
 * @brief Test class for PrimitiveTypeDescription and TypeDescription.
 *
 * NB: We cannot test TypeDescription without subclasses as it is abstract
 * and cannot be mocked because it relies on an abstract method in its
 * constructor. There's no way to mock methods called in the constructor
 * as the constructor is called before we get a chance to define mock method
 * return values.
 */

use PKP\filter\PrimitiveTypeDescription;

import('lib.pkp.tests.PKPTestCase');

class PrimitiveTypeDescriptionTest extends PKPTestCase
{
    /**
     * @covers PrimitiveTypeDescription
     * @covers TypeDescription
     */
    public function testInstantiateAndCheck()
    {
        $typeDescription = new PrimitiveTypeDescription('string');
        self::assertTrue($typeDescription->isCompatible($object = 'some string'));
        self::assertFalse($typeDescription->isCompatible($object = 5));
        self::assertFalse($typeDescription->isCompatible($object = [5]));

        self::assertEquals('string', $typeDescription->getTypeName());
        self::assertEquals('primitive::string', $typeDescription->getTypeDescription());

        $typeDescription = new PrimitiveTypeDescription('integer');
        self::assertTrue($typeDescription->isCompatible($object = 2));
        self::assertFalse($typeDescription->isCompatible($object = 'some string'));
        self::assertFalse($typeDescription->isCompatible($object = 5.5));
        self::assertFalse($typeDescription->isCompatible($object = new stdClass()));

        $typeDescription = new PrimitiveTypeDescription('float');
        self::assertTrue($typeDescription->isCompatible($object = 2.5));
        self::assertFalse($typeDescription->isCompatible($object = 'some string'));
        self::assertFalse($typeDescription->isCompatible($object = 5));

        $typeDescription = new PrimitiveTypeDescription('boolean');
        self::assertTrue($typeDescription->isCompatible($object = true));
        self::assertTrue($typeDescription->isCompatible($object = false));
        self::assertFalse($typeDescription->isCompatible($object = 1));
        self::assertFalse($typeDescription->isCompatible($object = ''));

        $typeDescription = new PrimitiveTypeDescription('integer[]');
        self::assertTrue($typeDescription->isCompatible($object = [2]));
        self::assertTrue($typeDescription->isCompatible($object = [2, 5]));
        self::assertFalse($typeDescription->isCompatible($object = 2));

        $typeDescription = new PrimitiveTypeDescription('integer[1]');
        self::assertTrue($typeDescription->isCompatible($object = [2]));
        self::assertFalse($typeDescription->isCompatible($object = [2, 5]));
        self::assertFalse($typeDescription->isCompatible($object = 2));
    }

    /**
     * @covers PrimitiveTypeDescription
     * @covers TypeDescription
     */
    public function testInstantiateWithInvalidTypeDescriptor1()
    {
        // An unknown type name will cause an error.
        $this->expectError();
        $typeDescription = new PrimitiveTypeDescription('xyz');
    }

    /**
     * @covers PrimitiveTypeDescription
     * @covers TypeDescription
     */
    public function testInstantiateWithInvalidTypeDescriptor2()
    {
        // We don't allow multi-dimensional arrays.
        $this->expectError();
        $typeDescription = new PrimitiveTypeDescription('integer[][]');
    }

    /**
     * @covers PrimitiveTypeDescription
     * @covers TypeDescription
     */
    public function testInstantiateWithInvalidTypeDescriptor3()
    {
        // An invalid cardinality will also cause an error.
        $this->expectError();
        $typeDescription = new PrimitiveTypeDescription('integer[x]');
    }
}
