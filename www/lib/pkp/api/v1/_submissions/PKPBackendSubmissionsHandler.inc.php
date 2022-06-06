<?php

/**
 * @file api/v1/_submissions/PKPBackendSubmissionsHandler.inc.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PKPBackendSubmissionsHandler
 * @ingroup api_v1_backend
 *
 * @brief Handle API requests for backend operations.
 *
 */

use APP\core\Application;
use APP\facades\Repo;
use APP\submission\Collector;
use PKP\db\DAORegistry;
use PKP\handler\APIHandler;
use PKP\plugins\HookRegistry;

use PKP\security\authorization\ContextAccessPolicy;
use PKP\security\authorization\SubmissionAccessPolicy;
use PKP\security\Role;
use Slim\Http\Response;

abstract class PKPBackendSubmissionsHandler extends APIHandler
{
    /** @var int Max items that can be requested */
    public const MAX_COUNT = 100;

    /**
     * Constructor
     */
    public function __construct()
    {
        $rootPattern = '/{contextPath}/api/{version}/_submissions';
        $this->_endpoints = array_merge_recursive($this->_endpoints, [
            'GET' => [
                [
                    'pattern' => "{$rootPattern}",
                    'handler' => [$this, 'getMany'],
                    'roles' => [
                        Role::ROLE_ID_SITE_ADMIN,
                        Role::ROLE_ID_MANAGER,
                        Role::ROLE_ID_SUB_EDITOR,
                        Role::ROLE_ID_AUTHOR,
                        Role::ROLE_ID_REVIEWER,
                        Role::ROLE_ID_ASSISTANT,
                    ],
                ],
            ],
            'DELETE' => [
                [
                    'pattern' => "{$rootPattern}/{submissionId:\d+}",
                    'handler' => [$this, 'delete'],
                    'roles' => [
                        Role::ROLE_ID_SITE_ADMIN,
                        Role::ROLE_ID_MANAGER,
                        Role::ROLE_ID_AUTHOR,
                    ],
                ],
            ],
        ]);
        parent::__construct();
    }

    /**
     * @copydoc PKPHandler::authorize()
     */
    public function authorize($request, &$args, $roleAssignments)
    {
        $this->addPolicy(new ContextAccessPolicy($request, $roleAssignments));

        $routeName = $this->getSlimRequest()->getAttribute('route')->getName();
        if (in_array($routeName, ['delete'])) {
            $this->addPolicy(new SubmissionAccessPolicy($request, $args, $roleAssignments));
        }

        return parent::authorize($request, $args, $roleAssignments);
    }

    /**
     * Get a collection of submissions
     *
     * @param Request $slimRequest Slim request object
     * @param Response $response object
     * @param array $args arguments
     *
     * @return Response
     */
    public function getMany($slimRequest, $response, $args)
    {
        $request = Application::get()->getRequest();
        $currentUser = $request->getUser();
        $context = $request->getContext();

        if (!$context) {
            return $response->withStatus(404)->withJsonError('api.404.resourceNotFound');
        }

        $collector = $this->getSubmissionCollector($slimRequest->getQueryParams());

        // Anyone not a manager or site admin can only access their assigned
        // submissions
        $userRoles = $this->getAuthorizedContextObject(ASSOC_TYPE_USER_ROLES);
        $canAccessUnassignedSubmission = !empty(array_intersect([Role::ROLE_ID_SITE_ADMIN, Role::ROLE_ID_MANAGER], $userRoles));
        HookRegistry::call('API::submissions::params', [$collector, $slimRequest]);
        if (!$canAccessUnassignedSubmission) {
            if (!is_array($collector->assignedTo)) {
                $collector->assignedTo([$currentUser->getId()]);
            } elseif ($collector->assignedTo != [$currentUser->getId()]) {
                return $response->withStatus(403)->withJsonError('api.submissions.403.requestedOthersUnpublishedSubmissions');
            }
        }

        $submissions = Repo::submission()->getMany($collector);

        $userGroupDao = DAORegistry::getDAO('UserGroupDAO'); /** @var UserGroupDAO $userGroupDao */
        $userGroups = $userGroupDao->getByContextId($context->getId())->toArray();

        /** @var GenreDAO $genreDao */
        $genreDao = DAORegistry::getDAO('GenreDAO');
        $genres = $genreDao->getByContextId($context->getId())->toArray();

        return $response->withJson([
            'itemsMax' => Repo::submission()->getCount($collector->limit(null)->offset(null)),
            'items' => Repo::submission()->getSchemaMap()->mapManyToSubmissionsList($submissions, $userGroups, $genres),
        ], 200);
    }

    /**
     * Configure a submission Collector based on the query params
     */
    protected function getSubmissionCollector(array $queryParams): Collector
    {
        $request = Application::get()->getRequest();
        $context = $request->getContext();

        $collector = Repo::submission()->getCollector()
            ->filterByContextIds([$context->getId()])
            ->limit(30)
            ->offset(0);

        foreach ($queryParams as $param => $val) {
            switch ($param) {
                case 'orderBy':
                    if (in_array($val, [
                        $collector::ORDERBY_DATE_PUBLISHED,
                        $collector::ORDERBY_DATE_SUBMITTED,
                        $collector::ORDERBY_LAST_ACTIVITY,
                        $collector::ORDERBY_LAST_MODIFIED,
                        $collector::ORDERBY_SEQUENCE,
                        $collector::ORDERBY_TITLE,
                    ])) {
                        $direction = isset($queryParams['orderDirection']) && $queryParams['orderDirection'] === $collector::ORDER_DIR_ASC
                            ? $collector::ORDER_DIR_ASC
                            : $collector::ORDER_DIR_DESC;
                        $collector->orderBy($val, $direction);
                    }
                    break;

                case 'status':
                    $collector->filterByStatus(array_map('intval', $this->paramToArray($val)));
                    break;

                case 'stageIds':
                    $collector->filterByStageIds(array_map('intval', $this->paramToArray($val)));
                    break;

                case 'categoryIds':
                    $collector->filterByCategoryIds(array_map('intval', $this->paramToArray($val)));
                    break;

                case 'assignedTo':
                    $val = array_map('intval', $this->paramToArray($val));
                    if ($val == [\PKP\submission\Collector::UNASSIGNED]) {
                        $val = array_shift($val);
                    }
                    $collector->assignedTo($val);
                    break;

                case 'daysInactive':
                    $collector->filterByDaysInactive((int) $val);
                    break;

                case 'offset':
                    $collector->offset((int) $val);
                    break;

                case 'searchPhrase':
                    $collector->searchPhrase($val);
                    break;

                case 'count':
                    $collector->limit(min(self::MAX_COUNT, (int) $val));
                    break;

                case 'isIncomplete':
                    $collector->filterByIncomplete(true);
                    break;

                case 'isOverdue':
                    $collector->filterByOverdue(true);
                    break;
            }
        }

        return $collector;
    }

    /**
     * Delete a submission
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
        $context = $request->getContext();
        $submissionId = (int) $args['submissionId'];
        $submission = Repo::submission()->get($submissionId);

        if (!$submission) {
            return $response->withStatus(404)->withJsonError('api.404.resourceNotFound');
        }

        if ($context->getId() != $submission->getContextId()) {
            return $response->withStatus(403)->withJsonError('api.submissions.403.deleteSubmissionOutOfContext');
        }

        if (!Repo::submission()->canCurrentUserDelete($submission)) {
            return $response->withStatus(403)->withJsonError('api.submissions.403.unauthorizedDeleteSubmission');
        }

        Repo::submission()->delete($submission);

        return $response->withJson(true);
    }
}
