<?php

namespace app\models\repository;

use Yii;

/**
 * This is the model class for table "routes".
 *
 * @property int $id
 * @property string $code
 * @property string $type
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property RouteStops[] $routeStops
 * @property Schedule[] $schedules
 */
class Route extends \yii\db\ActiveRecord
{
    public const TYPE_DIRECT = "direct";
    public const TYPE_REVERSE = "reverse";

    /**
     * Массив id остановок по порядку (виртуальное поле для формы)
     * @var int[]
     */
    public array $stop_ids = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return "routes";
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [["code", "type"], "required"],
            [["code"], "string", "max" => 16],
            [["created_at", "updated_at"], "safe"],
            [
                ["type"],
                "in",
                "range" => [self::TYPE_DIRECT, self::TYPE_REVERSE],
            ],
            [
                ["code", "type"],
                "unique",
                "targetAttribute" => ["code", "type"],
                "message" =>
                    "Для этого маршрута уже существует маршрут такого типа.",
            ],
            // поле для сохранения связей
            ["stop_ids", "required"],
            ["stop_ids", "each", "rule" => ["integer"]],
            ["stop_ids", "validateStopsCount"],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            "id" => "ID",
            "code" => "Номер/имя маршрута",
            "type" => "Тип маршрута",
            "created_at" => "Дата создания",
            "updated_at" => "Дата обновления",
        ];
    }

    public static function getTypeLabels(): array
    {
        return [
            self::TYPE_DIRECT => "Прямой",
            self::TYPE_REVERSE => "Обратный",
        ];
    }

    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = date("Y-m-d H:i:s");
            }
            $this->updated_at = date("Y-m-d H:i:s");
            return true;
        }
        return false;
    }

    /**
     * Gets query for [[RouteStops]].
     *
     * @return \yii\db\ActiveQuery|RouteStopsQuery
     */
    public function getRouteStops()
    {
        return $this->hasMany(RouteStops::class, ["route_id" => "id"]);
    }

    /**
     * Gets query for [[Schedules]].
     *
     * @return \yii\db\ActiveQuery|ScheduleQuery
     */
    public function getSchedules()
    {
        return $this->hasMany(Schedule::class, ["route_id" => "id"]);
    }

    /**
     * {@inheritdoc}
     * @return RouteQuery the active query used by this AR class.
     */
    public static function find(): RouteQuery
    {
        return new RouteQuery(get_called_class());
    }

    public function validateStopsCount($attribute)
    {
        $count = count(array_filter($this->$attribute));
        if ($count !== 10) {
            $this->addError($attribute, "У маршрута должно быть 10 остановок.");
        }
    }

    /**
     * Возвращает все маршруты имеющие расписание
     * @return Route[]
     */
    public static function withSchedules(): array
    {
        return self::find()->withSchedules()->all();
    }
}
