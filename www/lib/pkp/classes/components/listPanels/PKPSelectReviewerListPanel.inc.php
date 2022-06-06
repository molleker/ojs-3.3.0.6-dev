<?php
/**
 * @file components/listPanels/PKPSelectReviewerListPanel.inc.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PKPSelectReviewerListPanel
 * @ingroup classes_controllers_list
 *
 * @brief A class for loading a panel to select a reviewer.
 */

namespace PKP\components\listPanels;

use APP\facades\Repo;
use Illuminate\Support\Enumerable;

class PKPSelectReviewerListPanel extends ListPanel
{
    /** @var string URL to the API endpoint where items can be retrieved */
    public $apiUrl = '';

    /** @var int Number of items to show at one time */
    public $count = 30;

    /** @var array List of user IDs already assigned as a reviewer to this review round */
    public $currentlyAssigned = [];

    /** @var array Query parameters to pass if this list executes GET requests  */
    public $getParams = [];

    /** @var int Count of total items available for list */
    public $itemsMax = 0;

    /** @var string Name of the input field*/
    public $selectorName = '';

    /** @var array List of user IDs which may not be suitable for anonymous review because of existing access to author details */
    public $warnOnAssignment = [];

    /** @var Enumerable List of users who completed a review in the last round */
    public Enumerable $lastRoundReviewers;

    /**
     * @copydoc ListPanel::set()
     */
    public function set($args)
    {
        parent::set($args);
        $this->currentlyAssigned = !empty($args['currentlyAssigned']) ? $args['currentlyAssigned'] : $this->currentlyAssigned;
        $this->warnOnAssignment = !empty($args['warnOnAssignment']) ? $args['warnOnAssignment'] : $this->warnOnAssignment;
    }

    /**
     * @copydoc ListPanel::getConfig()
     */
    public function getConfig()
    {
        $config = parent::getConfig();
        $config['apiUrl'] = $this->apiUrl;
        $config['count'] = $this->count;
        $config['currentlyAssigned'] = $this->currentlyAssigned;
        $config['selectorName'] = $this->selectorName;
        $config['warnOnAssignment'] = $this->warnOnAssignment;
        $config['filters'] = [
            [
                'param' => 'reviewerRating',
                'title' => __('reviewer.list.filterRating'),
                'value' => 3,
                'min' => 1,
                'max' => 5,
                'useStars' => true,
                'valueLabel' => '{$value/5}',
            ],
            [
                'param' => 'reviewsCompleted',
                'title' => __('reviewer.list.completedReviews'),
                'value' => 10,
                'min' => 0,
                'max' => 20,
                'valueLabel' => __('common.moreThan'),
            ],
            [
                'param' => 'daysSinceLastAssignment',
                'title' => __('reviewer.list.daysSinceLastAssignmentDescription'),
                'value' => [0, 365],
                'min' => 0,
                'max' => 365,
                'filterType' => 'filter-slider-multirange',
                'valueLabel' => __('common.range'),
                'moreThanLabel' => __('common.moreThanOnly'),
                'lessThanLabel' => __('common.lessThanOnly'),
            ],
            [
                'param' => 'reviewsActive',
                'title' => __('reviewer.list.activeReviewsDescription'),
                'value' => [0, 20],
                'min' => 0,
                'max' => 20,
                'filterType' => 'filter-slider-multirange',
                'valueLabel' => __('common.range'),
                'moreThanLabel' => __('common.moreThanOnly'),
                'lessThanLabel' => __('common.lessThanOnly'),
            ],
            [
                'param' => 'averageCompletion',
                'title' => __('reviewer.list.averageCompletion'),
                'value' => 75,
                'min' => 0,
                'max' => 75,
                'valueLabel' => __('common.lessThan'),
            ],
        ];

        if (!empty($this->lastRoundReviewers)) {
            $config['lastRoundReviewers'] = Repo::user()
                ->getSchemaMap()
                ->summarizeManyReviewers($this->lastRoundReviewers)
                ->values()
                ->toArray();
        }

        if (!empty($this->getParams)) {
            $config['getParams'] = $this->getParams;
        }

        $config['itemsMax'] = $this->itemsMax;

        $config['activeReviewsCountLabel'] = __('reviewer.list.activeReviews');
        $config['activeReviewsLabel'] = __('reviewer.list.activeReviewsDescription');
        $config['assignedToLastRoundLabel'] = __('reviewer.list.assignedToLastRound');
        $config['averageCompletionLabel'] = __('reviewer.list.averageCompletion');
        $config['biographyLabel'] = __('reviewer.list.biography');
        $config['cancelledReviewsLabel'] = __('reviewer.list.cancelledReviews');
        $config['completedReviewsLabel'] = __('reviewer.list.completedReviews');
        $config['currentlyAssignedLabel'] = __('reviewer.list.currentlyAssigned');
        $config['daySinceLastAssignmentLabel'] = __('reviewer.list.daySinceLastAssignment');
        $config['daysSinceLastAssignmentLabel'] = __('reviewer.list.daysSinceLastAssignment');
        $config['daysSinceLastAssignmentDescriptionLabel'] = __('reviewer.list.daysSinceLastAssignmentDescription');
        $config['declinedReviewsLabel'] = __('reviewer.list.declinedReviews');
        $config['emptyLabel'] = __('reviewer.list.empty');
        $config['gossipLabel'] = __('user.gossip');
        $config['neverAssignedLabel'] = __('reviewer.list.neverAssigned');
        $config['reassignLabel'] = __('reviewer.list.reassign');
        $config['reassignWithNameLabel'] = __('reviewer.list.reassign.withName');
        $config['reviewerRatingLabel'] = __('reviewer.list.reviewerRating');
        $config['reviewInterestsLabel'] = __('reviewer.list.reviewInterests');
        $config['selectReviewerLabel'] = __('editor.submission.selectReviewer');
        $config['warnOnAssignmentLabel'] = __('reviewer.list.warnOnAssign');
        $config['warnOnAssignmentUnlockLabel'] = __('reviewer.list.warnOnAssignUnlock');

        return $config;
    }

    /**
     * Helper method to get the items property according to the self::$getParams
     *
     * @param Request $request
     *
     * @return array
     */
    public function getItems($request)
    {
        $reviewers = Repo::user()->getMany($this->_getCollector());
        $items = [];
        $map = Repo::user()->getSchemaMap();
        foreach ($reviewers as $reviewer) {
            $items[] = $map->summarizeReviewer($reviewer);
        }

        return $items;
    }

    /**
     * Helper method to get the itemsMax property according to self::$getParams
     *
     * @return int
     */
    public function getItemsMax()
    {
        $collector = $this->_getCollector();
        return Repo::user()->getCount($collector->offset(null)->limit(null));
    }

    /**
     * Helper method to compile initial params to get items
     *
     * @return array
     */
    protected function _getCollector()
    {
        $params = $this->getParams;
        return Repo::user()->getCollector()
            ->filterByContextIds([$params['contextId']])
            ->filterByWorkflowStageIds([$params['reviewStage']])
            ->filterByRoleIds([\PKP\security\Role::ROLE_ID_REVIEWER])
            ->includeReviewerData()
            ->offset(null)
            ->limit($this->count);
    }
}
