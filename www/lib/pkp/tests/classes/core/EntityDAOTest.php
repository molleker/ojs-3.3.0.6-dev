<?php

/**
 * @file tests/classes/core/EntityDAOTest.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class EntityDAOTest
 * @ingroup tests_classes_core
 *
 * @see EntityDAO
 *
 * @brief Tests for the EntityDAO class.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use PKP\core\DataObject;
use PKP\plugins\HookRegistry;

import('lib.pkp.tests.PKPTestCase');

class TestEntityDAO extends \PKP\core\EntityDAO
{
    /** @copydoc EntityDAO::$schema */
    public $schema = 'test_schema';

    /** @copydoc EntityDAO::$table */
    public $table = 'test_entity';

    /** @copydoc EntityDAO::$settingsTable */
    public $settingsTable = 'test_entity_settings';

    /** @copydoc EntityDAO::$primarykeyColumn */
    public $primaryKeyColumn = 'test_id';

    /** @copydoc EntityDAO::$primaryTableColumns */
    public $primaryTableColumns = [
        'id' => 'test_id',
        'integerColumn' => 'integer_column',
        'nullableIntegerColumn' => 'nullable_integer_column',
    ];

    /**
     * @copydoc EntityDAO::_insert()
     */
    public function insert(DataObject $testEntity): int
    {
        return parent::_insert($testEntity);
    }

    /**
     * @copydoc EntityDAO::update()
     */
    public function update(DataObject $testEntity)
    {
        parent::_update($testEntity);
    }

    /**
     * @copydoc EntityDAO::_delete()
     */
    public function delete(DataObject $testEntity)
    {
        parent::_delete($testEntity);
    }

    public function newDataObject()
    {
        return new DataObject();
    }
}

class EntityDAOTest extends PKPTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Create the database tables
        Schema::create('test_entity', function (Blueprint $table) {
            $table->bigInteger('test_id')->autoIncrement();
            $table->bigInteger('integer_column')->nullable(false);
            $table->bigInteger('nullable_integer_column')->nullable(true);
        });
        Schema::create('test_entity_settings', function (Blueprint $table) {
            $table->bigInteger('test_id');
            $table->foreign('test_id')->references('test_id')->on('test_entity');
            $table->string('locale', 14)->default('');
            $table->string('setting_name', 255);
            $table->text('setting_value')->nullable();
            $table->string('setting_type', 6)->nullable();
            $table->index(['test_id'], 'test_entity_settings_test_id');
            $table->unique(['test_id', 'locale', 'setting_name'], 'test_entity_settings_pkey');
        });

        // Inject a test schema
        HookRegistry::register('Schema::get::test_schema', function ($hookName, $args) {
            $schema = & $args[0];
            $schema = json_decode('{
                "title": "Test Schema",
                "description": "A schema for testing purposes",
                "properties": {
                    "id": {
                        "type": "integer",
                        "readOnly": true
                    },
                    "integerColumn": {
                        "type": "integer"
                    },
                    "nullableIntegerColumn": {
                        "type": "integer",
                        "validation": ["nullable"]
                    },
                    "nonlocalizedSettingString": {
                        "type": "string"
                    }
                }
            }');
            return true;
        });
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        Schema::dropIfExists('test_entity_settings');
        Schema::dropIfExists('test_entity');
    }

    /**
     * @covers EntityDAO::_insert
     * @covers EntityDAO::get
     */
    public function testCRUD()
    {
        $testEntityDao = app(TestEntityDAO::class);

        // Create a data object for storage
        $testEntity = new DataObject();
        $testEntity->setData('integerColumn', 3);
        $testEntity->setData('nullableIntegerColumn', 4);
        $testEntity->setData('nonlocalizedSettingString', 'test string');

        // Store the data object to the DB
        $testEntityDao->insert($testEntity);
        $insertedId = $testEntity->getId();
        self::assertNotNull($insertedId);

        // Retrieve the data object from the DB
        $fetchedEntity = $testEntityDao->get($insertedId);

        // Ensure that the stored data matches the retrieved data
        self::assertEquals($testEntity->_data, $fetchedEntity->_data);
        unset($fetchedEntity);

        // Update some values
        $testEntity->setData('integerColumn', 5);
        $testEntity->setData('nonlocalizedSettingString', 'another test string');
        $testEntity->setData('nullableIntegerColumn', null);
        $testEntityDao->update($testEntity);

        $fetchedEntity = $testEntityDao->get($insertedId);
        self::assertEquals([
            'id' => $insertedId,
            'integerColumn' => 5,
            'nonlocalizedSettingString' => 'another test string',
            'nullableIntegerColumn' => null,
        ], $fetchedEntity->_data);

        // Delete the entity and make sure it's gone.
        $testEntityDao->delete($testEntity);
        $fetchedEntity = $testEntityDao->get($insertedId);
        self::assertNull($fetchedEntity);
    }

    public function testNullablePrimaryColumn()
    {
        $testEntityDao = app(TestEntityDAO::class);

        // Create a data object for storage
        $testEntity = new DataObject();
        $testEntity->setData('integerColumn', 3);
        $testEntity->setData('nullableIntegerColumn', null);

        // Store the data object to the DB
        $testEntityDao->insert($testEntity);
        $insertedId = $testEntity->getId();

        // Retrieve the data object from the DB
        $fetchedEntity = $testEntityDao->get($insertedId);

        // Ensure that the stored data matches the retrieved data
        self::assertTrue(!isset($fetchedEntity->_data['nullableIntegerColumn']), 'Nullable columns are stored properly');

        // Delete the entity and make sure it's gone.
        $testEntityDao->delete($testEntity);
    }

    public function testNotNullablePrimaryColumn()
    {
        $testEntityDao = app(TestEntityDAO::class);

        // Create a data object for storage
        $testEntity = new DataObject();
        $testEntity->setData('integerColumn', null); // Invalid

        $this->expectException(\Exception::class);

        // Store the data object to the DB
        $testEntityDao->insert($testEntity);
    }
}
