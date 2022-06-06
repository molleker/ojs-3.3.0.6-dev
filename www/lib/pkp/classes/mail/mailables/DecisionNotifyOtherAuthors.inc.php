<?php

/**
 * @file classes/mail/mailables/DecisionNotifyOtherAuthors.inc.php
 *
 * Copyright (c) 2014-2022 Simon Fraser University
 * Copyright (c) 2000-2022 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class DecisionNotifyOtherAuthors
 *
 * @brief Email sent to other authors of a submission when an editorial decision
 *   is made. These are authors with metadata records on the publication who are
 *   not assigned as participants to the submission workflow.
 */

namespace PKP\mail\mailables;

use APP\submission\Submission;
use PKP\context\Context;
use PKP\mail\Mailable;
use PKP\mail\traits\Sender;

class DecisionNotifyOtherAuthors extends Mailable
{
    use Sender;

    /** @var string An email variable that contains the message that was sent to the submitting author */
    public const MESSAGE_TO_SUBMITTING_AUTHOR = 'messageToSubmittingAuthor';

    protected static ?string $name = 'mailable.decision.notifyOtherAuthors.name';
    protected static ?string $description = 'mailable.decision.notifyOtherAuthors.description';
    protected static ?string $emailTemplateKey = 'EDITOR_DECISION_NOTIFY_OTHER_AUTHORS';
    protected static bool $supportsTemplates = true;
    protected static array $groupIds = [
        self::GROUP_SUBMISSION,
        self::GROUP_REVIEW,
        self::GROUP_COPYEDITING,
        self::GROUP_PRODUCTION,
    ];

    public function __construct(Context $context, Submission $submission)
    {
        parent::__construct(func_get_args());
    }

    public static function getDataDescriptions(): array
    {
        $variables = parent::getDataDescriptions();
        $variables[self::MESSAGE_TO_SUBMITTING_AUTHOR] = __('mailable.decision.notifyOtherAuthors.variable.message.description');
        return $variables;
    }
}
