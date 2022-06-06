<?php

/**
 * @file classes/task/StatisticsReport.inc.php
 *
 * Copyright (c) 2013-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class StatisticsReport
 * @ingroup tasks
 *
 * @brief Class responsible to send the monthly statistics report.
 */

namespace PKP\task;

use APP\core\Application;
use DateTimeImmutable;
use PKP\db\DAORegistry;
use PKP\notification\managerDelegate\EditorialReportNotificationManager;
use PKP\notification\PKPNotification;

use PKP\scheduledTask\ScheduledTask;
use PKP\security\Role;

class StatisticsReport extends ScheduledTask
{
    /** @var array List of roles that might be notified */
    private $_roleIds = [Role::ROLE_ID_MANAGER, Role::ROLE_ID_SUB_EDITOR];

    /** @var int Quantity of emails to accept for each batch */
    private $_sleepEvery = 100;

    /** @var int Defines how many seconds the script will wait between each batch */
    private $_sleepBy = 2;


    /**
     * @copydoc ScheduledTask::getName()
     */
    public function getName(): string
    {
        return __('admin.scheduledTask.statisticsReport');
    }

    /**
     * @copydoc ScheduledTask::executeActions()
     */
    public function executeActions(): bool
    {
        @set_time_limit(0);

        $contextDao = Application::get()->getContextDAO();
        $userGroupDao = DAORegistry::getDAO('UserGroupDAO'); /** @var UserGroupDAO $userGroupDao */

        $sentMessages = 0;
        for ($contexts = $contextDao->getAll(true); $context = $contexts->next();) {
            $editorialReportNotificationManager = new EditorialReportNotificationManager(PKPNotification::NOTIFICATION_TYPE_EDITORIAL_REPORT);
            $editorialReportNotificationManager->initialize(
                $context,
                new DateTimeImmutable('first day of previous month midnight'),
                new DateTimeImmutable('first day of this month midnight')
            );
            $notifiedUsersSet = [];
            foreach ($this->_roleIds as $roleId) {
                for ($userGroups = $userGroupDao->getByRoleId($context->getId(), $roleId); $userGroup = $userGroups->next();) {
                    for ($users = $userGroupDao->getUsersById($userGroup->getId(), $context->getId()); $user = $users->next();) {
                        if (isset($notifiedUsersSet[$user->getId()])) {
                            continue;
                        }
                        $editorialReportNotificationManager->notify($user);
                        $notifiedUsersSet[$user->getId()] = 0;
                        if (!(++$sentMessages % $this->_sleepEvery)) {
                            sleep($this->_sleepBy);
                        }
                    }
                }
            }
        }
        return true;
    }
}

if (!PKP_STRICT_MODE) {
    class_alias('\PKP\task\StatisticsReport', '\StatisticsReport');
}
