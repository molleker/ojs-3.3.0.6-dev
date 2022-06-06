<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\LocaleContent;

/**
 * localeContentSearch represents the model behind the search form about `app\models\LocaleContent`.
 */
class localeContentSearch extends LocaleContent
{

    public function attributes()
    {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['file.name']);
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'fileId'], 'integer'],
            [['key', 'content','file.name'], 'safe'],
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


            $query = LocaleContent::find()->with('file');
       // } else {
           // $query=Yii::$app->db->createCommand('SELECT *,MATCH (content) AGAINST () FROM '.LocaleContent::className().' ')
      //  }
         //  $query->joinWith('file');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        ;

        if (!($this->load($params) && $this->validate())) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'fileId' => $this->fileId,
        ]);

     //   if (strlen($this->getAttribute('content'))>0)
       //     $query->addSelect(array('*,MATCH (content) AGAINST ("'.$this->getAttribute('content').'")'));




        $query->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'content', $this->content]);


        //$query->andFilterWhere()

        $query->joinWith(['file'=>function ($q) {
            $q->where(' localeFiles.id = ' .intval($this->getAttribute('file.name')). ' OR '.intval($this->getAttribute('file.name')).'=0');
        }]);

        return $dataProvider;
    }
}
