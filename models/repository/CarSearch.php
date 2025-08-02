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
    public string $brand_country = '';

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'fare', 'production_year', 'owner_id', 'driver_id', 'brand_id'], 'integer'],
            [['created_at', 'updated_at', 'model'], 'safe'],
            [['brand_country'], 'string'],
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
        $query = Car::find();

        // Присоединяем таблицу брендов всегда (для сортировки и фильтрации)
        $query->joinWith(['brand']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // Сортировка по brand_country (car_brand.country)
        $dataProvider->setSort([
            'attributes' => [
                'id',
                'fare',
                'production_year',
                'model',
                'owner_id',
                'driver_id',
                'brand_id',
                'created_at',
                'updated_at',
                'brand_country' => [
                    'asc' => ['car_brand.country' => SORT_ASC],
                    'desc' => ['car_brand.country' => SORT_DESC],
                    'default' => SORT_ASC,
                    'label' => 'Страна производителя',
                ],
            ],
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
            'brand_id' => $this->brand_id,
        ]);

        $query->andFilterWhere(['ilike', 'model', $this->model]);

        if ($this->created_at) {
            $query->andFilterWhere(['like', "to_char(created_at, 'YYYY-MM-DD HH24:MI:SS')", $this->created_at]);
        }

        if ($this->updated_at) {
            $query->andFilterWhere(['like', "to_char(updated_at, 'YYYY-MM-DD HH24:MI:SS')", $this->updated_at]);
        }

        if (strlen(trim($this->brand_country)) > 0) {
            $query->andFilterWhere(['ilike', 'car_brand.country', $this->brand_country]);
        }

        return $dataProvider;
    }
}
