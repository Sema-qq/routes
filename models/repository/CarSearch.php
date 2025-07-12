<?php

namespace app\models\repository;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\repository\Car;

/**
 * CarSearch represents the model behind the search form of `app\models\repository\Car`.
 */
class CarSearch extends Car
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'fare', 'production_year', 'owner_id', 'driver_id'], 'integer'],
            [['brand', 'manufacturer', 'country', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null): ActiveDataProvider
    {
        $query = Car::find();

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
            'fare' => $this->fare,
            'production_year' => $this->production_year,
            'owner_id' => $this->owner_id,
            'driver_id' => $this->driver_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['ilike', 'brand', $this->brand])
            ->andFilterWhere(['ilike', 'manufacturer', $this->manufacturer])
            ->andFilterWhere(['ilike', 'country', $this->country]);

        return $dataProvider;
    }
}
