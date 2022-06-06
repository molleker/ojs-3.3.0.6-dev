<?php
/**
 * @file classes/decision/types/traits/NotifyReviewers.inc.php
 *
 * Copyright (c) 2014-2022 Simon Fraser University
 * Copyright (c) 2000-2022 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class decision
 *
 * @brief Helper functions for decisions that may request a payment
 */

namespace PKP\decision\types\traits;

use APP\core\Application;
use APP\facades\Repo;
use APP\log\SubmissionEventLogEntry;
use APP\submission\Submission;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Validator;
use PKP\core\Core;
use PKP\db\DAORegistry;
use PKP\log\SubmissionLog;
use PKP\mail\EmailData;
use PKP\mail\Mailable;
use PKP\mail\mailables\DecisionNotifyReviewer;
use PKP\submission\reviewAssignment\ReviewAssignment;
use PKP\submission\reviewAssignment\ReviewAssignmentDAO;
use PKP\user\User;

trait NotifyReviewers
{
    protected string $ACTION_NOTIFY_REVIEWERS = 'notifyReviewers';

    /** @copydoc DecisionType::addEmailDataToMailable() */
    abstract protected function addEmailDataToMailable(Mailable $mailable, User $user, EmailData $email): Mailable;

    /** @copydoc DecisionType::getAssignedAuthorIds() */
    abstract protected function getAssignedAuthorIds(Submission $submission): array;

    /** @copydoc InExternalReviewRound::getCompletedReviewerIds() */
    abstract protected function getCompletedReviewerIds(Submission $submission, int $reviewRoundId): array;

    /** @copydoc DecisionType::setRecipientError() */
    abstract protected function setRecipientError(string $actionErrorKey, array $invalidRecipientIds, Validator $validator);

    /**
     * Validate the decision action to notify reviewers
     */
    protected function validateNotifyReviewersAction(array $action, string $actionErrorKey, Validator $validator, Submission $submission, int $reviewRoundId)
    {
        $errors = $this->validateEmailAction($action, $submission, $this->getAllowedAttachmentFileStages());
        foreach ($errors as $key => $propErrors) {
            foreach ($propErrors as $propError) {
                $validator->errors()->add($actionErrorKey . '.' . $key, $propError);
            }
        }
        if (empty($action['recipients'])) {
            $validator->errors()->add($actionErrorKey . '.recipients', __('validator.required'));
            return;
        }
        $reviewerIds = $this->getCompletedReviewerIds($submission, $reviewRoundId);
        $invalidRecipients = array_diff($action['recipients'], $reviewerIds);
        if (count($invalidRecipients)) {
            $this->setRecipientError($actionErrorKey, $invalidRecipients, $validator);
        }
    }

    /**
     * Send the email to the reviewers
     */
    protected function sendReviewersEmail(DecisionNotifyReviewer $mailable, EmailData $email, User $editor, Submission $submission)
    {
        /** @var DecisionNotifyReviewer $mailable */
        $mailable = $this->addEmailDataToMailable($mailable, $editor, $email);

        /** @var User[] $recipients */
        $recipients = array_map(function ($userId) {
            return Repo::user()->get($userId);
        }, $email->recipients);

        foreach ($recipients as $recipient) {
            Mail::send($mailable->recipients([$recipient], $email->locale));

            // Update the ReviewAssignment to indicate the reviewer has been acknowledged
            /** @var ReviewAssignmentDAO $reviewAssignmentDao */
            $reviewAssignmentDao = DAORegistry::getDAO('ReviewAssignmentDAO');
            $reviewAssignment = $reviewAssignmentDao->getReviewAssignment($mailable->getDecision()->getData('reviewRoundId'), $recipient->getId());
            if ($reviewAssignment) {
                $reviewAssignment->setDateAcknowledged(Core::getCurrentDate());
                $reviewAssignment->stampModified();
                $reviewAssignment->setUnconsidered(ReviewAssignment::REVIEW_ASSIGNMENT_NOT_UNCONSIDERED);
                $reviewAssignmentDao->updateObject($reviewAssignment);
            }
        }

        SubmissionLog::logEvent(
            Application::get()->getRequest(),
            $submission,
            SubmissionEventLogEntry::SUBMISSION_LOG_DECISION_EMAIL_SENT,
            'submission.event.decisionReviewerEmailSent',
            [
                'recipientCount' => count($recipients),
                'subject' => $email->subject,
            ]
        );
    }
}
