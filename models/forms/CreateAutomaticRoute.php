<?php

namespace app\models\forms;

use app\models\repository\Route;
use app\models\repository\RouteStops;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * Форма для создания автоматического маршрута (обратного или прямого)
 * на основе существующего маршрута
 */
class CreateAutomaticRoute extends Model
{
    public const ERR_ATTR = 'error'; // виртуальный атрибут для сообщений

    /**
     * Создает новый маршрут противоположного типа
     * @return Route|null созданный маршрут или null в случае ошибки
     */
    public function createRoute(Route $sourceRoute): ?Route
    {
        // Определяем противоположный тип
        $oppositeType = $sourceRoute->type === Route::TYPE_DIRECT
            ? Route::TYPE_REVERSE
            : Route::TYPE_DIRECT;

        // Проверяем, что маршрут противоположного типа еще не существует
        $existingRoute = Route::findOne(['code' => $sourceRoute->code, 'type' => $oppositeType]);
        if ($existingRoute) {
            $this->addError(self::ERR_ATTR, 'Маршрут противоположного типа уже существует');
            return null;
        }

        $sourceStops = $sourceRoute->getRouteStops()
            ->orderBy(['stop_number' => SORT_ASC])
            ->all();

        $routeStopIds = array_column($sourceStops, 'id');
        if (count($routeStopIds) != 10) {
            $this->addError(self::ERR_ATTR, 'У текущеего маршрута меньше 10 остановок');
            return null;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Создаем новый маршрут
            $newRoute = new Route();
            $newRoute->code = $sourceRoute->code;
            $newRoute->type = $oppositeType;
            $newRoute->stop_ids = $routeStopIds; // for validation (it's bad, but "se la vie")

            if (!$newRoute->save()) {
                $this->addErrors($newRoute->errors);
                throw new Exception('Не удалось сохранить новый маршрут');
            }

            // Создаем остановки в обратном порядке
            $totalStops = count($sourceStops);
            foreach ($sourceStops as $index => $sourceStop) {
                $newRouteStop = new RouteStops();
                $newRouteStop->route_id = $newRoute->id;
                $newRouteStop->stop_id = $sourceStop->stop_id;
                // Обращаем порядок: первая становится последней и наоборот
                $newRouteStop->stop_number = $totalStops - $index;

                if (!$newRouteStop->save()) {
                    throw new Exception('Не удалось сохранить остановку маршрута');
                }
            }

            $transaction->commit();
            return $newRoute;

        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError(self::ERR_ATTR, 'Ошибка при создании маршрута: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Проверяет, можно ли создать автоматический маршрут для данного маршрута
     * @param Route $route
     * @return Route
     */
    public static function GetReverseRoute(Route $route): ?Route
    {
        $reverseType = $route->type === Route::TYPE_DIRECT
            ? Route::TYPE_REVERSE
            : Route::TYPE_DIRECT;

        return Route::findOne(['code' => $route->code, 'type' => $reverseType]);
    }

    /**
     * Возвращает текст для кнопки создания автоматического маршрута
     * @param Route $route
     * @return string
     */
    public static function getButtonText(Route $route): string
    {
        $reverseTypeLabel = mb_strtolower(Route::getTypeLabels()[$route->type], 'UTF-8');
        return "Создать {$reverseTypeLabel} маршрут автоматически";
    }
}
