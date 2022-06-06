<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "article_files".
 *
 * @property integer $file_id
 * @property integer $revision
 * @property integer $source_file_id
 * @property integer $source_revision
 * @property integer $subission_id
 * @property string $file_name
 * @property string $file_type
 * @property integer $file_size
 * @property string $original_file_name
 * @property integer $file_stage
 * @property integer $viewable
 * @property string $date_uploaded
 * @property string $date_modified
 * @property integer $round
 * @property integer $assoc_id
 */
class ArticleFiles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'submission_files';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['revision'], 'required'],
            [['revision', 'source_file_id', 'source_revision', 'submission_id', 'file_size', 'file_stage', 'viewable', 'round', 'assoc_id'], 'integer'],
            [['date_uploaded', 'date_modified'], 'safe'],
            [['file_name'], 'string', 'max' => 90],
            [['file_type'], 'string', 'max' => 255],
            [['original_file_name'], 'string', 'max' => 127],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'file_id' => 'File ID',
            'revision' => 'Revision',
            'source_file_id' => 'Source File ID',
            'source_revision' => 'Source Revision',
            'submission_id' => 'Article ID',
            'file_name' => 'File Name',
            'file_type' => 'File Type',
            'file_size' => 'File Size',
            'original_file_name' => 'Original File Name',
            'file_stage' => 'File Stage',
            'viewable' => 'Viewable',
            'date_uploaded' => 'Date Uploaded',
            'date_modified' => 'Date Modified',
            'round' => 'Round',
            'assoc_id' => 'Assoc ID',
        ];
    }

    public function getArticle()
    {
        return $this->hasOne(Articles::className(), ['submission_id' => 'submission_id']);
    }
}
