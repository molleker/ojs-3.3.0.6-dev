<?php

/**
 * @file api/v1/announcements/PKPAnnouncementHandler.inc.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PKPAnnouncementHandler
 * @ingroup api_v1_announcement
 *
 * @brief Handle API requests for announcement operations.
 *
 */

use APP\core\Application;
use APP\facades\Repo;
use APP\notification\Notification;
use PKP\db\DAORegistry;
use PKP\handler\APIHandler;
use PKP\notification\managerDelegate\AnnouncementNotificationManager;
use PKP\plugins\HookRegistry;
use PKP\security\authorization\PolicySet;
use PKP\security\authorization\RoleBasedHandlerOperationPolicy;
use PKP\security\Role;

use PKP\services\PKPSchemaService;

class PKPAnnouncementHandler extends APIHandler
{
    /** @var int The default number of announcements to return in one request */
    public const DEFAULT_COUNT = 30;

    /** @var int The maxium number of announcements to return in one request */
    public const MAX_COUNT = 100;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_handlerPath = 'announcements';
        $this->_endpoints = [
            'GET' => [
                [
                    'pattern' => $this->getEndpointPattern(),
                    'handler' => [$this, 'getMany'],
                    'roles' => [Role::ROLE_ID_MANAGER, Role::ROLE_ID_SITE_ADMIN],
                ],
                [
                    'pattern' => $this->getEndpointPattern() . '/{announcementId:\d+}',
                    'handler' => [$this, 'get'],
                    'roles' => [Role::ROLE_ID_MANAGER, Role::ROLE_ID_SITE_ADMIN],
                ],
            ],
            'POST' => [
                [
                    'pattern' => $this->getEndpointPattern(),
                    'handler' => [$this, 'add'],
                    'roles' => [Role::ROLE_ID_MANAGER, Role::ROLE_ID_SITE_ADMIN],
                ],
            ],
            'PUT' => [
                [
                    'pattern' => $this->getEndpointPattern() . '/{announcementId:\d+}',
                    'handler' => [$this, 'edit'],
                    'roles' => [Role::ROLE_ID_MANAGER, Role::ROLE_ID_SITE_ADMIN],
                ],
            ],
            'DELETE' => [
                [
                    'pattern' => $this->getEndpointPattern() . '/{announcementId:\d+}',
                    'handler' => [$this, 'delete'],
                    'roles' => [Role::ROLE_ID_MANAGER, Role::ROLE_ID_SITE_ADMIN],
                ],
            ],
        ];
        parent::__construct();
    }

    /**
     * @copydoc PKPHandler::authorize
     */
    public function authorize($request, &$args, $roleAssignments)
    {
        $rolePolicy = new PolicySet(PolicySet::COMBINING_PERMIT_OVERRIDES);

        foreach ($roleAssignments as $role => $operations) {
            $rolePolicy->addPolicy(new RoleBasedHandlerOperationPolicy($request, $role, $operations));
        }
        $this->addPolicy($rolePolicy);

        return parent::authorize($request, $args, $roleAssignments);
    }

    /**
     * Get a single submission
     *
     * @param Request $slimRequest Slim request object
     * @param Response $response object
     * @param array $args arguments
     *
     * @return Response
     */
    public function get($slimRequest, $response, $args)
    {
        $announcement = Repo::announcement()->get((int) $args['announcementId']);

        if (!$announcement) {
            return $response->withStatus(404)->withJsonError('api.announcements.404.announcementNotFound');
        }

        // The assocId in announcements should always point to the contextId
        if ($announcement->getData('assocId') !== $this->getRequest()->getContext()->getId()) {
            return $response->withStatus(404)->withJsonError('api.announcements.400.contextsNotMatched');
        }

        return $response->withJson(Repo::announcement()->getSchemaMap()->map($announcement), 200);
    }

    /**
     * Get a collection of announcements
     *
     * @param Request $slimRequest Slim request object
     * @param Response $response object
     * @param array $args arguments
     *
     * @return Response
     */
    public function getMany($slimRequest, $response, $args)
    {
        $collector = Repo::announcement()->getCollector()
            ->limit(self::DEFAULT_COUNT)
            ->offset(0);

        foreach ($slimRequest->getQueryParams() as $param => $val) {
            switch ($param) {
                case 'typeIds':
                    $collector->filterByTypeIds(
                        array_map('intval', $this->paramToArray($val))
                    );
                    break;
                case 'count':
                    $collector->limit(min((int) $val, self::MAX_COUNT));
                    break;
                case 'offset':
                    $collector->offset((int) $val);
                    break;
                case 'searchPhrase':
                    $collector->searchPhrase($val);
                    break;
            }
        }

        $collector->filterByContextIds([$this->getRequest()->getContext()->getId()]);

        HookRegistry::call('API::submissions::params', [$collector, $slimRequest]);

        $announcements = Repo::announcement()->getMany($collector);

        return $response->withJson([
            'itemsMax' => Repo::announcement()->getCount($collector->limit(null)->offset(null)),
            'items' => Repo::announcement()->getSchemaMap()->summarizeMany($announcements),
        ], 200);
    }

    /**
     * Add an announcement
     *
     * @param Request $slimRequest Slim request object
     * @param Response $response object
     * @param array $args arguments
     *
     * @return Response
     */
    public function add($slimRequest, $response, $args)
    {
        $request = $this->getRequest();

        if (!$request->getContext()) {
            throw new Exception('You can not add an announcement without sending a request to the API endpoint of a particular context.');
        }

        $params = $this->convertStringsToSchema(PKPSchemaService::SCHEMA_ANNOUNCEMENT, $slimRequest->getParsedBody());
        $params['assocType'] = Application::get()->getContextAssocType();
        $params['assocId'] = $request->getContext()->getId();

        $primaryLocale = $request->getContext()->getPrimaryLocale();
        $allowedLocales = $request->getContext()->getSupportedFormLocales();
        $errors = Repo::announcement()->validate(null, $params, $allowedLocales, $primaryLocale);

        if (!empty($errors)) {
            return $response->withStatus(400)->withJson($errors);
        }

        $announcement = Repo::announcement()->newDataObject($params);
        $id = Repo::announcement()->add($announcement);
        $announcement = Repo::announcement()->get($id);

        if (filter_var($params['sendEmail'], FILTER_VALIDATE_BOOLEAN)) {
            import('lib.pkp.classes.notification.managerDelegate.AnnouncementNotificationManager');
            $announcementNotificationManager = new AnnouncementNotificationManager(Notification::NOTIFICATION_TYPE_NEW_ANNOUNCEMENT);
            $announcementNotificationManager->initialize($announcement);

            $notificationSubscriptionSettingsDao = DAORegistry::getDAO('NotificationSubscriptionSettingsDAO'); /** @var NotificationSubscriptionSettingsDAO $notificationSubscriptionSettingsDao  */
            $userGroupDao = DAORegistry::getDAO('UserGroupDAO'); /** @var UserGroupDAO $userGroupDao */
            $allUsers = $userGroupDao->getUsersByContextId($request->getContext()->getId());
            while ($user = $allUsers->next()) {
                if ($user->getDisabled()) {
                    continue;
                }
                $blockedEmails = $notificationSubscriptionSettingsDao->getNotificationSubscriptionSettings('blocked_emailed_notification', $user->getId(), $request->getContext()->getId());
                if (!in_array(Notification::NOTIFICATION_TYPE_NEW_ANNOUNCEMENT, $blockedEmails)) {
                    $announcementNotificationManager->notify($user);
                }
            }
        }

        return $response->withJson(Repo::announcement()->getSchemaMap()->map($announcement), 200);
    }

    /**
     * Edit an announcement
     *
     * @param Request $slimRequest Slim request object
     * @param Response $response object
     * @param array $args arguments
     *
     * @return Response
     */
    public function edit($slimRequest, $response, $args)
    {
        $request = $this->getRequest();

        $announcement = Repo::announcement()->get((int) $args['announcementId']);

        if (!$announcement) {
            return $response->withStatus(404)->withJsonError('api.announcements.404.announcementNotFound');
        }

        if ($announcement->getData('assocType') !== Application::get()->getContextAssocType()) {
            throw new Exception('Announcement has an assocType that did not match the context.');
        }

        // Don't allow to edit an announcement from one context from a different context's endpoint
        if ($request->getContext()->getId() !== $announcement->getData('assocId')) {
            return $response->withStatus(403)->withJsonError('api.announcements.400.contextsNotMatched');
        }

        $params = $this->convertStringsToSchema(PKPSchemaService::SCHEMA_ANNOUNCEMENT, $slimRequest->getParsedBody());
        $params['id'] = $announcement->getId();
        $params['typeId'] ??= null;

        $context = $request->getContext();
        $primaryLocale = $context->getPrimaryLocale();
        $allowedLocales = $context->getSupportedFormLocales();

        $errors = Repo::announcement()->validate($announcement, $params, $allowedLocales, $primaryLocale);
        if (!empty($errors)) {
            return $response->withStatus(400)->withJson($errors);
        }

        Repo::announcement()->edit($announcement, $params);

        $announcement = Repo::announcement()->get($announcement->getId());

        return $response->withJson(Repo::announcement()->getSchemaMap()->map($announcement), 200);
    }

    /**
     * Delete an announcement
     *
     * @param Request $slimRequest Slim request object
     * @param Response $response object
     * @param array $args arguments
     *
     * @return Response
     */
    public function delete($slimRequest, $response, $args)
    {
        $request = $this->getRequest();

        $announcement = Repo::announcement()->get((int) $args['announcementId']);

        if (!$announcement) {
            return $response->withStatus(404)->withJsonError('api.announcements.404.announcementNotFound');
        }

        if ($announcement->getData('assocType') !== Application::get()->getContextAssocType()) {
            throw new Exception('Announcement has an assocType that did not match the context.');
        }

        // Don't allow to delete an announcement from one context from a different context's endpoint
        if ($request->getContext()->getId() !== $announcement->getData('assocId')) {
            return $response->withStatus(403)->withJsonError('api.announcements.400.contextsNotMatched');
        }

        $announcementProps = Repo::announcement()->getSchemaMap()->map($announcement);

        Repo::announcement()->delete($announcement);

        return $response->withJson($announcementProps, 200);
    }
}
