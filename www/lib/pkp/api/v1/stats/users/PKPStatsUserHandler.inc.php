<?php

/**
 * @file api/v1/stats/PKPStatsUserHandler.inc.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PKPStatsUserHandler
 * @ingroup api_v1_stats
 *
 * @brief Handle API requests for publication statistics.
 *
 */

use APP\facades\Repo;
use PKP\handler\APIHandler;
use PKP\plugins\HookRegistry;
use PKP\security\authorization\ContextAccessPolicy;
use PKP\security\authorization\PolicySet;
use PKP\security\authorization\RoleBasedHandlerOperationPolicy;
use PKP\security\Role;

class PKPStatsUserHandler extends APIHandler
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_handlerPath = 'stats/users';
        $this->_endpoints = [
            'GET' => [
                [
                    'pattern' => $this->getEndpointPattern(),
                    'handler' => [$this, 'get'],
                    'roles' => [Role::ROLE_ID_SITE_ADMIN, Role::ROLE_ID_MANAGER, Role::ROLE_ID_SUB_EDITOR],
                ],
            ],
        ];
        parent::__construct();
    }

    /**
     * @copydoc PKPHandler::authorize()
     */
    public function authorize($request, &$args, $roleAssignments)
    {
        $this->addPolicy(new ContextAccessPolicy($request, $roleAssignments));

        $rolePolicy = new PolicySet(PolicySet::COMBINING_PERMIT_OVERRIDES);
        foreach ($roleAssignments as $role => $operations) {
            $rolePolicy->addPolicy(new RoleBasedHandlerOperationPolicy($request, $role, $operations));
        }
        $this->addPolicy($rolePolicy);

        return parent::authorize($request, $args, $roleAssignments);
    }

    /**
     * Get user stats
     *
     * Returns the count of users broken down by roles
     *
     * @param Request $slimRequest Slim request object
     * @param object $response Response
     * @param array $args
     *
     * @return object Response
     */
    public function get($slimRequest, $response, $args)
    {
        $request = $this->getRequest();

        if (!$request->getContext()) {
            return $response->withStatus(404)->withJsonError('api.404.resourceNotFound');
        }

        $collector = Repo::user()->getCollector();
        foreach ($slimRequest->getQueryParams() as $param => $value) {
            switch ($param) {
                case 'registeredAfter': $collector->filterRegisteredAfter($value); break;
                case 'registeredBefore': $collector->filterRegisteredBefore($value); break;
                case 'status': switch ($value) {
                    case 'disabled':
                        $collector->filterByStatus($collector::STATUS_DISABLED);
                        break;
                    case 'all':
                        $collector->filterByStatus($collector::STATUS_ALL);
                        break;
                    default:
                    case 'active':
                        $collector->filterByStatus($collector::STATUS_ACTIVE);
                        break;
                }
            }
        }

        HookRegistry::call('API::stats::users::params', [$collector, $slimRequest]);

        $collector->filterByContextIds([$request->getContext()->getId()]);

        if ($result !== true) {
            return $response->withStatus(400)->withJsonError($result);
        }

        return $response->withJson(array_map(
            function ($item) {
                $item['name'] = __($item['name']);
                return $item;
            },
            Repo::user()->getRolesOverview($collector)
        ));
    }
}
