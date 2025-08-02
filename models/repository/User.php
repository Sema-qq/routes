<?php

namespace app\models\repository;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $full_name
 * @property string|null $license_date
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Car[] $cars
 * @property Car[] $cars0
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['full_name'], 'required'],
            [['license_date'], 'default', 'value' => null],
            [['license_date'], 'validateLicenseDate'],
            [['license_date', 'created_at', 'updated_at'], 'safe'],
            [['full_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'full_name' => 'ФИО',
            'license_date' => 'Дата получения В.У.',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = date('Y-m-d H:i:s');
            }
            $this->updated_at = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }

    /**
     * Gets query for [[Car]].
     *
     * @return \yii\db\ActiveQuery|CarQuery
     */
    public function getOwnerCars()
    {
        return $this->hasMany(Car::class, ['owner_id' => 'id']);
    }

    /**
     * Gets query for [[Cars0]].
     *
     * @return \yii\db\ActiveQuery|CarQuery
     */
    public function getDriverCars()
    {
        return $this->hasMany(Car::class, ['driver_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return UserQuery the active query used by this AR class.
     */
    public static function find(): UserQuery
    {
        return new UserQuery(get_called_class());
    }

    /**
     * Возвращает водителей со стажем больше 3 лет.
     * @return User[]
     */
    public static function getDrivers(): array
    {
        $threeYearsAgo = date('Y-m-d', strtotime('-3 years'));
        return self::find()
            ->where(['<=', 'license_date', $threeYearsAgo])
            ->andWhere(['IS NOT', 'license_date', null])
            ->all();
    }

    public function cantBeDeleted(): bool
    {
        if ($this->getOwnerCars()->exists()) {
            return true;
        }

        if ($this->getDriverCars()->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Запрещаем изменять водилки на невалидные, если водитель уже назначен куда-то
     * @param $attribute
     * @return void
     */
    public function validateLicenseDate($attribute)
    {
        if ($this->getDriverCars()->exists() && $this->$attribute) {
            $threeYearsAgo = strtotime('-3 years');
            $enteredDate = strtotime($this->$attribute);
            if ($enteredDate > $threeYearsAgo) {
                $this->addError($attribute, 'Дата В.У. не может быть моложе 3 лет, так как пользователь уже назначен водителем маршрутки.');
            }
        }
    }
}
