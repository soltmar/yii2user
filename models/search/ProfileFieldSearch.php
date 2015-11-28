<?php

namespace marsoltys\yii2user\models\search;

use marsoltys\yii2user\models\ProfileField;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserSearch represents the model behind the search form about `app\models\User`.
 */
class ProfileFieldSearch extends ProfileField
{
    public function rules()
    {
        return [
            [['varname', 'title', 'field_type', 'field_size', 'required', 'position', 'visible'], 'safe'],
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
        $query = ProfileField::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination'=> [
                'pageSize'=>Yii::$app->controller->module->fields_page_size,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }



        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'varname', $this->varname])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'field_type', $this->field_type])
            ->andFilterWhere(['like', 'field_size', $this->field_size])
            ->andFilterWhere(['like', 'field_size_min', $this->field_size_min])
            ->andFilterWhere(['like', 'required', $this->required])
            ->andFilterWhere(['like', 'match', $this->match])
            ->andFilterWhere(['like', 'range', $this->range])
            ->andFilterWhere(['like', 'error_message', $this->error_message])
            ->andFilterWhere(['like', 'other_validator', $this->other_validator])
            ->andFilterWhere(['like', 'default', $this->default])
            ->andFilterWhere(['like', 'widget', $this->widget])
            ->andFilterWhere(['like', 'widgetparams', $this->widgetparams])
            ->andFilterWhere(['like', 'position', $this->position])
            ->andFilterWhere(['like', 'visible', $this->visible]);

        return $dataProvider;
    }
}
