<?php

namespace app\models\repository;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\repository\Schedule;

/**
 * ScheduleStopsSearch represents the model behind the search form of `app\models\repository\Schedule`.
 */
class ScheduleStopsSearch extends Schedule
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'car_id', 'route_id', 'stop_id', 'stop_number', 'boarded_count'], 'integer'],
            [['date', 'planned_time', 'actual_time'], 'safe'],
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
        $query = Schedule::find();

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
            'date' => $this->date,
            'car_id' => $this->car_id,
            'route_id' => $this->route_id,
            'stop_id' => $this->stop_id,
            'stop_number' => $this->stop_number,
            'planned_time' => $this->planned_time,
            'actual_time' => $this->actual_time,
            'boarded_count' => $this->boarded_count,
        ]);

        return $dataProvider;
    }
}
