<?php

/**
 * @file classes/galley/Collector.inc.php
 *
 * Copyright (c) 2014-2022 Simon Fraser University
 * Copyright (c) 2000-2022 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class galley
 *
 * @brief A helper class to configure a Query Builder to get a collection of galleys
 */

namespace PKP\galley;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use PKP\core\interfaces\CollectorInterface;
use PKP\plugins\HookRegistry;

class Collector implements CollectorInterface
{
    public DAO $dao;

    public ?array $publicationIds = null;

    public ?array $contextIds = null;

    public function __construct(DAO $dao)
    {
        $this->dao = $dao;
    }

    public function filterByPublicationIds(?array $publicationIds): self
    {
        $this->publicationIds = $publicationIds;
        return $this;
    }

    public function filterByContextIds(?array $contextIds): self
    {
        $this->contextIds = $contextIds;
        return $this;
    }

    public function getQueryBuilder(): Builder
    {
        $qb = DB::table($this->dao->table . ' as g')
            ->when(!is_null($this->publicationIds), function ($qb) {
                $qb->whereIn('g.publication_id', $this->publicationIds);
            })
            ->when(!is_null($this->contextIds), function ($qb) {
                $qb->join('publications as p', 'p.publication_id', '=', 'g.publication_id')
                    ->leftJoin('submissions as s', 's.submission_id', '=', 'p.submission_id')
                    ->whereIn('s.context_id', $this->contextIds);
            })
            ->orderBy('g.seq', 'asc');

        HookRegistry::call('Galley::Collector', [&$qb, $this]);

        return $qb;
    }
}
