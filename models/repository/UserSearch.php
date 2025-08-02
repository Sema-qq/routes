<?php

namespace app\models\repository;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\repository\User;

/**
 * UserSearch represents the model behind the search form of `app\models\repository\User`.
 */
class UserSearch extends User
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['full_name', 'license_date', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null): ActiveDataProvider
    {
        $query = User::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        if ($this->license_date) {
            $query->andFilterWhere(['like', "to_char(license_date, 'YYYY-MM-DD')", $this->license_date]);
        }

        if ($this->created_at) {
            $query->andFilterWhere(['like', "to_char(created_at, 'YYYY-MM-DD HH24:MI:SS')", $this->created_at]);
        }

        if ($this->updated_at) {
            $query->andFilterWhere(['like', "to_char(updated_at, 'YYYY-MM-DD HH24:MI:SS')", $this->updated_at]);
        }

        $query->andFilterWhere(['ilike', 'full_name', $this->full_name]);

        return $dataProvider;
    }
}
