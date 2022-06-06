<?php

declare(strict_types=1);

/**
 * @file classes/observers/events/UnpublishedEvent.inc.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class UnpublishedEvent
 * @ingroup core
 *
 * @brief Event fired when publication is being unpublished
 */

namespace PKP\observers\events;

use Illuminate\Foundation\Events\Dispatchable;
use PKP\observers\traits\Publishable;

class UnpublishedEvent
{
    use Dispatchable;
    use Publishable;
}
