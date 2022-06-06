<?php
/**
 * @file classes/announcement/Repository.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Repository
 *
 * @brief A repository to find and manage announcements.
 */

namespace PKP\announcement;

use APP\core\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use PKP\core\Core;
use PKP\plugins\HookRegistry;
use PKP\services\PKPSchemaService;
use PKP\validation\ValidatorFactory;

class Repository
{
    /** @var DAO $dao */
    public $dao;

    /** @var string $schemaMap The name of the class to map this entity to its schema */
    public $schemaMap = maps\Schema::class;

    /** @var Request $request */
    protected $request;

    /** @var PKPSchemaService $schemaService */
    protected $schemaService;


    public function __construct(DAO $dao, Request $request, PKPSchemaService $schemaService)
    {
        $this->dao = $dao;
        $this->request = $request;
        $this->schemaService = $schemaService;
    }

    /** @copydoc DAO::newDataObject() */
    public function newDataObject(array $params = []): Announcement
    {
        $object = $this->dao->newDataObject();
        if (!empty($params)) {
            $object->setAllData($params);
        }
        return $object;
    }

    /** @copydoc DAO::get() */
    public function get(int $id): ?Announcement
    {
        return $this->dao->get($id);
    }

    /** @copydoc DAO::getCount() */
    public function getCount(Collector $query): int
    {
        return $this->dao->getCount($query);
    }

    /** @copydoc DAO::getIds() */
    public function getIds(Collector $query): Collection
    {
        return $this->dao->getIds($query);
    }

    /** @copydoc DAO::getMany() */
    public function getMany(Collector $query): LazyCollection
    {
        return $this->dao->getMany($query);
    }

    /** @copydoc DAO::getCollector() */
    public function getCollector(): Collector
    {
        return app(Collector::class);
    }

    /**
     * Get an instance of the map class for mapping
     * announcements to their schema
     */
    public function getSchemaMap(): maps\Schema
    {
        return app('maps')->withExtensions($this->schemaMap);
    }

    /**
     * Validate properties for an announcement
     *
     * Perform validation checks on data used to add or edit an announcement.
     *
     * @param array $props A key/value array with the new data to validate
     * @param array $allowedLocales The context's supported locales
     * @param string $primaryLocale The context's primary locale
     *
     * @return array A key/value array with validation errors. Empty if no errors
     */
    public function validate(?Announcement $object, array $props, array $allowedLocales, string $primaryLocale): array
    {
        $validator = ValidatorFactory::make(
            $props,
            $this->schemaService->getValidationRules($this->dao->schema, $allowedLocales),
            [
                'dateExpire.date_format' => __('stats.dateRange.invalidDate'),
            ]
        );

        // Check required fields if we're adding a context
        ValidatorFactory::required(
            $validator,
            $object,
            $this->schemaService->getRequiredProps($this->dao->schema),
            $this->schemaService->getMultilingualProps($this->dao->schema),
            $allowedLocales,
            $primaryLocale
        );

        // Check for input from disallowed locales
        ValidatorFactory::allowedLocales($validator, $this->schemaService->getMultilingualProps($this->dao->schema), $allowedLocales);

        $errors = [];

        if ($validator->fails()) {
            $errors = $this->schemaService->formatValidationErrors($validator->errors());
        }

        HookRegistry::call('Announcement::validate', [&$errors, $object, $props, $allowedLocales, $primaryLocale]);

        return $errors;
    }

    /** @copydoc DAO::insert() */
    public function add(Announcement $announcement): int
    {
        $announcement->setData('datePosted', Core::getCurrentDate());
        $id = $this->dao->insert($announcement);
        HookRegistry::call('Announcement::add', [$announcement]);

        return $id;
    }

    /** @copydoc DAO::update() */
    public function edit(Announcement $announcement, array $params)
    {
        $newAnnouncement = clone $announcement;
        $newAnnouncement->setAllData(array_merge($newAnnouncement->_data, $params));

        HookRegistry::call('Announcement::edit', [$newAnnouncement, $announcement, $params]);

        $this->dao->update($newAnnouncement);
    }

    /** @copydoc DAO::delete() */
    public function delete(Announcement $announcement)
    {
        HookRegistry::call('Announcement::delete::before', [$announcement]);
        $this->dao->delete($announcement);
        HookRegistry::call('Announcement::delete', [$announcement]);
    }

    /**
     * Delete a collection of announcements
     */
    public function deleteMany(Collector $collector)
    {
        $announcements = $this->getMany($collector);
        foreach ($announcements as $announcement) {
            $this->delete($announcement);
        }
    }
}
