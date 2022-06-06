<?php

/**
 * @file classes/mail/mailables/DecisionRevertInitialDeclineNotifyAuthor.inc.php
 *
 * Copyright (c) 2014-2022 Simon Fraser University
 * Copyright (c) 2000-2022 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class DecisionRevertInitialDeclineNotifyAuthor
 *
 * @brief Email sent to the author(s) when a SUBMISSION_EDITOR_DECISION_INITIAL_DECLINE
 *  decision is made.
 */

namespace PKP\mail\mailables;

use APP\decision\Decision;
use APP\submission\Submission;
use PKP\context\Context;
use PKP\mail\Mailable;
use PKP\mail\traits\Recipient;
use PKP\mail\traits\Sender;

class DecisionRevertInitialDeclineNotifyAuthor extends Mailable
{
    use Recipient;
    use Sender;

    protected static ?string $name = 'mailable.decision.revertInitialDecline.notifyAuthor.name';
    protected static ?string $description = 'mailable.decision.revertInitialDecline.notifyAuthor.description';
    protected static ?string $emailTemplateKey = 'EDITOR_DECISION_REVERT_INITIAL_DECLINE';
    protected static bool $supportsTemplates = true;
    protected static array $groupIds = [self::GROUP_SUBMISSION];

    public function __construct(Context $context, Submission $submission, Decision $decision)
    {
        parent::__construct(func_get_args());
    }
}
