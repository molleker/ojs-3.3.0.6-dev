<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AntiplagiatReports;
use yii\helpers\ArrayHelper;

/**
 * AntiplagiatReportsSearch represents the model behind the search form about `app\models\AntiplagiatReports`.
 */
class AntiplagiatReportsSearch extends AntiplagiatReports
{
    public $article_name;
    public $submission_id;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'file_id', 'status_id'], 'integer'],
            [['text', 'created_at', 'updated_at', 'link'], 'safe'],
            [['score', 'submission_id'], 'number'],
            [['article_name'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AntiplagiatReports::find();

        $query->with([
            'file.article.titleSetting',
            'file.article.journal',
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->submission_id) {
            $files = ArticleFiles::findAll(['submission_id' => $this->submission_id]);
            $file_ids = ArrayHelper::getColumn($files, 'file_id');
            $reports = AntiplagiatReports::findAll(['file_id' => $file_ids]);
            $query->andWhere(['id' => ArrayHelper::getColumn($reports, 'id')]);
        }

        if ($this->article_name) {
            $article_ids = ArrayHelper::getColumn(ArticleSettings::find()
                ->where(['setting_name' => 'title'])
                ->filterWhere(['like', 'setting_value', $this->article_name])
                ->all(), 'submission_id')
            ;

            $files = ArticleFiles::findAll(['submission_id' => $article_ids]);
            $file_ids = ArrayHelper::getColumn($files, 'file_id');
            $reports = AntiplagiatReports::findAll(['file_id' => $file_ids]);
            $query->andWhere(['id' => ArrayHelper::getColumn($reports, 'id')]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'file_id' => $this->file_id,
            'status_id' => $this->status_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'score' => $this->score,
        ]);

        $query->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'link', $this->link]);

        return $dataProvider;
    }
}
