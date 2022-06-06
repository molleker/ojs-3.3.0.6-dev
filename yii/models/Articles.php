<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "articles".
 *
 * @property integer $submission_id
 * @property string $locale
 * @property integer $user_id
 * @property integer $journal_id
 * @property integer $section_id
 * @property string $language
 * @property string $comments_to_ed
 * @property string $citations
 * @property string $date_submitted
 * @property string $last_modified
 * @property string $date_status_modified
 * @property integer $status
 * @property integer $submission_progress
 * @property integer $current_round
 * @property integer $submission_file_id
 * @property integer $revised_file_id
 * @property integer $review_file_id
 * @property integer $editor_file_id
 * @property string $pages
 * @property integer $fast_tracked
 * @property integer $hide_author
 * @property integer $comments_status
 * @property integer $edit_submission_file
 */
class Articles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'submissions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'context_id', 'section_id', 'status', 'submission_progress', 'current_round', 'submission_file_id', 'revised_file_id', 'review_file_id', 'editor_file_id', 'fast_tracked', 'hide_author', 'comments_status', 'edit_submission_file'], 'integer'],
            [['comments_to_ed', 'citations'], 'string'],
            [['date_submitted', 'last_modified', 'date_status_modified'], 'safe'],
            [['locale'], 'string', 'max' => 5],
            [['language'], 'string', 'max' => 10],
            [['pages'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'submission_id' => 'Article ID',
            'locale' => 'Locale',
            'user_id' => 'User ID',
            'context_id' => 'Journal ID',
            'section_id' => 'Section ID',
            'language' => 'Language',
            'comments_to_ed' => 'Comments To Ed',
            'citations' => 'Citations',
            'date_submitted' => 'Date Submitted',
            'last_modified' => 'Last Modified',
            'date_status_modified' => 'Date Status Modified',
            'status' => 'Status',
            'submission_progress' => 'Submission Progress',
            'current_round' => 'Current Round',
            'submission_file_id' => 'Submission File ID',
            'revised_file_id' => 'Revised File ID',
            'review_file_id' => 'Review File ID',
            'editor_file_id' => 'Editor File ID',
            'pages' => 'Pages',
            'fast_tracked' => 'Fast Tracked',
            'hide_author' => 'Hide Author',
            'comments_status' => 'Comments Status',
            'edit_submission_file' => 'Edit Submission File',
        ];
    }

    public function getTitleSetting()
    {
        return $this->hasOne(ArticleSettings::className(), ['submission_id' => 'submission_id'])->where(['setting_name' => 'title']);
    }

    public function getJournal()
    {
        return $this->hasOne(Journals::className(), ['journal_id' => 'context_id']);
    }
}
