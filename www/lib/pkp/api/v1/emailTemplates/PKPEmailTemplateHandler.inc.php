<?php
/**
 * @file api/v1/emailTemplates/PKPEmailTemplateHandler.inc.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PKPEmailTemplateHandler
 * @ingroup api_v1_email_templates
 *
 * @brief Base class to handle API requests for email templates.
 */

use PKP\facades\Repo;
use PKP\handler\APIHandler;
use PKP\plugins\HookRegistry;
use PKP\security\authorization\ContextRequiredPolicy;
use PKP\security\authorization\PolicySet;
use PKP\security\authorization\RoleBasedHandlerOperationPolicy;
use PKP\security\Role;
use PKP\services\PKPSchemaService;
use Slim\Http\Request as SlimRequest;
use Slim\Http\Response;

class PKPEmailTemplateHandler extends APIHandler
{
    /**
     * @copydoc APIHandler::__construct()
     */
    public function __construct()
    {
        $this->_handlerPath = 'emailTemplates';
        $roles = [Role::ROLE_ID_SITE_ADMIN, Role::ROLE_ID_MANAGER];
        $this->_endpoints = [
            'GET' => [
                [
                    'pattern' => $this->getEndpointPattern(),
                    'handler' => [$this, 'getMany'],
                    'roles' => array_merge($roles, [Role::ROLE_ID_SUB_EDITOR, ROLE::ROLE_ID_ASSISTANT]),
                ],
                [
                    'pattern' => $this->getEndpointPattern() . '/{key}',
                    'handler' => [$this, 'get'],
                    'roles' => array_merge($roles, [Role::ROLE_ID_SUB_EDITOR, ROLE::ROLE_ID_ASSISTANT]),
                ],
            ],
            'POST' => [
                [
                    'pattern' => $this->getEndpointPattern(),
                    'handler' => [$this, 'add'],
                    'roles' => $roles,
                ],
            ],
            'PUT' => [
                [
                    'pattern' => $this->getEndpointPattern() . '/{key}',
                    'handler' => [$this, 'edit'],
                    'roles' => $roles,
                ],
            ],
            'DELETE' => [
                [
                    'pattern' => $this->getEndpointPattern() . '/restoreDefaults',
                    'handler' => [$this, 'restoreDefaults'],
                    'roles' => $roles,
                ],
                [
                    'pattern' => $this->getEndpointPattern() . '/{key}',
                    'handler' => [$this, 'delete'],
                    'roles' => $roles,
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

        // This endpoint is not available at the site-wide level
        $this->addPolicy(new ContextRequiredPolicy($request));

        foreach ($roleAssignments as $role => $operations) {
            $rolePolicy->addPolicy(new RoleBasedHandlerOperationPolicy($request, $role, $operations));
        }
        $this->addPolicy($rolePolicy);

        return parent::authorize($request, $args, $roleAssignments);
    }

    /**
     * Get a collection of email templates
     */
    public function getMany(SlimRequest $slimRequest, Response $response, array $args): Response
    {
        $request = $this->getRequest();

        $collector = Repo::emailTemplate()->getCollector();

        // Process query params to format incoming data as needed
        foreach ($slimRequest->getQueryParams() as $param => $val) {
            switch ($param) {
                case 'isModified':
                    $collector->filterByIsModified((bool) $val);
                    break;
                case 'isCustom':
                    $collector->filterByIsCustom((bool) $val);
                    break;
                case 'isEnabled':
                    $collector->filterByIsEnabled((bool) $val);
                    break;
                case 'fromRoleIds':
                    $collector->filterByFromRoleIds(array_map('intval', $this->paramToArray($val)));
                    break;
                case 'toRoleIds':
                    $collector->filterByToRoleIds(array_map('intval', $this->paramToArray($val)));
                    break;
                case 'stageIds':
                    $collector->filterByStageIds(array_map('intval', $this->paramToArray($val)));
                    break;
                case 'searchPhrase':
                    $collector->searchPhrase(trim($val));
                    break;
                case 'count':
                    $collector->limit((int) $val);
                    break;
                case 'offset':
                    $collector->offset((int) $val);
                    break;
            }
        }

        HookRegistry::call('API::emailTemplates::params', [$collector, $slimRequest]);

        // Always restrict results to the current context
        $collector->filterByContext($request->getContext()->getId());

        $emailTemplatesCollection = Repo::emailTemplate()->getMany($collector);

        return $response->withJson([
            'itemsMax' => Repo::emailTemplate()->getCount($collector->limit(null)->offset(null)),
            'items' => Repo::emailTemplate()->getSchemaMap()->summarizeMany($emailTemplatesCollection),
        ], 200);
    }

    /**
     * Get a single email template
     *
     * @param Request $slimRequest Slim request object
     * @param Response $response object
     * @param array $args arguments
     *
     * @return Response
     */
    public function get($slimRequest, $response, $args)
    {
        $request = $this->getRequest();

        $emailTemplate = Repo::emailTemplate()->getByKey($request->getContext()->getId(), $args['key']);

        if (!$emailTemplate) {
            return $response->withStatus(404)->withJsonError('api.emailTemplates.404.templateNotFound');
        }

        return $response->withJson(Repo::emailTemplate()->getSchemaMap()->map($emailTemplate), 200);
    }

    /**
     * Add an email template
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
        $requestContext = $request->getContext();

        $params = $this->convertStringsToSchema(PKPSchemaService::SCHEMA_EMAIL_TEMPLATE, $slimRequest->getParsedBody());

        if (!isset($params['contexId'])) {
            $params['contextId'] = $requestContext->getId();
        }

        $primaryLocale = $requestContext->getData('primaryLocale');
        $allowedLocales = $requestContext->getData('supportedFormLocales');
        $errors = Repo::emailTemplate()->validate(null, $params, $allowedLocales, $primaryLocale);

        if (!empty($errors)) {
            return $response->withStatus(400)->withJson($errors);
        }

        $emailTemplate = Repo::emailTemplate()->newDataObject($params);
        Repo::emailTemplate()->add($emailTemplate);
        $emailTemplate = Repo::emailTemplate()->getByKey($emailTemplate->getData('contextId'), $emailTemplate->getData('key'));

        return $response->withJson(Repo::emailTemplate()->getSchemaMap()->map($emailTemplate), 200);
    }

    /**
     * Edit an email template
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
        $requestContext = $request->getContext();

        $emailTemplate = Repo::emailTemplate()->getByKey($requestContext->getId(), $args['key']);

        if (!$emailTemplate) {
            return $response->withStatus(404)->withJsonError('api.emailTemplates.404.templateNotFound');
        }

        $params = $this->convertStringsToSchema(PKPSchemaService::SCHEMA_EMAIL_TEMPLATE, $slimRequest->getParsedBody());
        $params['key'] = $args['key'];

        // Only allow admins to change the context an email template is attached to.
        // Set the contextId if it has not been npassed or the user is not an admin
        $userRoles = $this->getAuthorizedContextObject(ASSOC_TYPE_USER_ROLES);
        if (isset($params['contextId'])
                && !in_array(Role::ROLE_ID_SITE_ADMIN, $userRoles)
                && $params['contextId'] !== $requestContext->getId()) {
            return $response->withStatus(403)->withJsonError('api.emailTemplates.403.notAllowedChangeContext');
        } elseif (!isset($params['contextId'])) {
            $params['contextId'] = $requestContext->getId();
        }

        $errors = Repo::emailTemplate()->validate(
            $emailTemplate,
            $params,
            $requestContext->getData('supportedFormLocales'),
            $requestContext->getData('primaryLocale')
        );

        if (!empty($errors)) {
            return $response->withStatus(400)->withJson($errors);
        }

        Repo::emailTemplate()->edit($emailTemplate, $params);

        $emailTemplate = Repo::emailTemplate()->getByKey(
            // context ID is null if edited for the first time
            $emailTemplate->getData('contextId') ?? $params['contextId'],
            $emailTemplate->getData('key')
        );

        return $response->withJson(Repo::emailTemplate()->getSchemaMap()->map($emailTemplate), 200);
    }

    /**
     * Delete an email template
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
        $requestContext = $request->getContext();

        $emailTemplate = Repo::emailTemplate()->getByKey($requestContext->getId(), $args['key']);

        // Only custom email templates can be deleted, so return 404 if no id exists
        if (!$emailTemplate || !$emailTemplate->getData('id')) {
            return $response->withStatus(404)->withJsonError('api.emailTemplates.404.templateNotFound');
        }

        $props = Repo::emailTemplate()->getSchemaMap()->map($emailTemplate);
        Repo::emailTemplate()->delete($emailTemplate);

        return $response->withJson($props, 200);
    }

    /**
     * Restore defaults in the email template settings
     *
     * @param Request $slimRequest Slim request object
     * @param Response $response object
     * @param array $args arguments
     *
     * @return Response
     */
    public function restoreDefaults($slimRequest, $response, $args)
    {
        $contextId = $this->getRequest()->getContext()->getId();
        $deletedKeys = Repo::emailTemplate()->restoreDefaults($contextId);
        return $response->withJson($deletedKeys, 200);
    }
}
