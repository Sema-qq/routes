<?php

namespace app\controllers;

use app\models\forms\ScheduleCreateForm;
use app\models\repository\Schedule;
use app\models\repository\ScheduleSearch;
use app\models\repository\ScheduleGroup;
use app\models\repository\ScheduleGroupSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * ScheduleController implements the CRUD actions for Schedule model.
 */
class ScheduleController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            "verbs" => [
                "class" => VerbFilter::className(),
                "actions" => [
                    "delete" => ["POST"],
                ],
            ],
        ]);
    }

    /**
     * Lists all Schedule groups (grouped by route and date).
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new ScheduleGroupSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render("index", [
            "searchModel" => $searchModel,
            "dataProvider" => $dataProvider,
        ]);
    }

    /**
     * Displays a schedule group with all its stops.
     * @param string $date Date
     * @param int $car_id Car ID
     * @param int $route_id Route ID
     * @return string
     * @throws NotFoundHttpException if the schedule group cannot be found
     */
    public function actionView(string $date, int $car_id, int $route_id): string
    {
        // Находим первую запись расписания для получения основной информации
        $mainSchedule = Schedule::find()
            ->where([
                "date" => $date,
                "car_id" => $car_id,
                "route_id" => $route_id,
            ])
            ->orderBy(["stop_number" => SORT_ASC])
            ->one();

        if (!$mainSchedule) {
            throw new NotFoundHttpException("Расписание не найдено.");
        }

        // Получаем все записи расписания для этой группы
        $scheduleDetails = Schedule::find()
            ->where([
                "date" => $date,
                "car_id" => $car_id,
                "route_id" => $route_id,
            ])
            ->orderBy(["stop_number" => SORT_ASC])
            ->all();

        return $this->render("view", [
            "model" => $mainSchedule,
            "scheduleDetails" => $scheduleDetails,
        ]);
    }

    /**
     * Мастер создания расписания - начальный экран с выбором даты
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ScheduleCreateForm();
        $model->loadFromSession();

        if ($this->request->isPost) {
            $model->load($this->request->post());

            if (!empty($model->date)) {
                $model->current_step = 1;
                $model->saveToSession();
                return $this->redirect(["create-step", "step" => 1]);
            }
        } else {
            // Устанавливаем дату по умолчанию на сегодня
            $model->date = date("Y-m-d");
        }

        return $this->render("create/start", [
            "model" => $model,
        ]);
    }

    /**
     * Шаги мастера создания расписания
     * @param int $step Номер шага (1-4)
     * @return string|Response
     */
    public function actionCreateStep($step)
    {
        $step = (int) $step;
        if ($step < 1 || $step > 4) {
            return $this->redirect(["create"]);
        }

        $model = new ScheduleCreateForm();
        if (!$model->loadFromSession()) {
            return $this->redirect(["create"]);
        }

        // Проверяем, можно ли перейти к этому шагу
        if (!$model->canGoToStep($step)) {
            // Перенаправляем на первый доступный шаг
            for ($i = 1; $i <= 4; $i++) {
                if ($model->canGoToStep($i)) {
                    return $this->redirect(["create-step", "step" => $i]);
                }
            }
            return $this->redirect(["create"]);
        }

        $model->current_step = $step;

        if ($this->request->isPost) {
            $model->load($this->request->post());

            // Валидация в зависимости от шага
            $isValid = $this->validateStep($model, $step);

            if ($isValid) {
                // Обработка кнопок навигации
                if (isset($_POST["next"])) {
                    if ($step < 4) {
                        $model->nextStep();
                        $model->saveToSession();
                        return $this->redirect([
                            "create-step",
                            "step" => $step + 1,
                        ]);
                    }
                } elseif (isset($_POST["previous"])) {
                    if ($step > 1) {
                        $model->previousStep();
                        $model->saveToSession();
                        return $this->redirect([
                            "create-step",
                            "step" => $step - 1,
                        ]);
                    }
                } elseif (isset($_POST["finish"])) {
                    // Финальная валидация и сохранение
                    if ($model->validate()) {
                        if ($model->createSchedule()) {
                            Yii::$app->session->setFlash(
                                "success",
                                "Расписание успешно создано для всех 10 остановок.",
                            );
                            return $this->redirect(["index"]);
                        }
                    }
                }
            }
        }

        return $this->render("create/step{$step}", [
            "model" => $model,
        ]);
    }

    /**
     * Возврат к определенному шагу мастера
     * @param int $step
     * @return Response
     */
    public function actionCreateGoToStep(int $step): Response
    {
        $step = (int) $step;
        if ($step < 1 || $step > 4) {
            return $this->redirect(["create"]);
        }

        $model = new ScheduleCreateForm();
        if (!$model->loadFromSession()) {
            return $this->redirect(["create"]);
        }

        if ($model->canGoToStep($step)) {
            $model->setStep($step);
            return $this->redirect(["create-step", "step" => $step]);
        }

        return $this->redirect(["create-step", "step" => $model->current_step]);
    }

    /**
     * Отмена мастера и очистка сессии
     * @return Response
     */
    public function actionCreateCancel(): Response
    {
        $model = new ScheduleCreateForm();
        $model->clearSession();
        Yii::$app->session->setFlash("info", "Создание расписания отменено.");
        return $this->redirect(["index"]);
    }

    /**
     * Валидация конкретного шага
     * @param ScheduleCreateForm $model
     * @param int $step
     * @return bool
     */
    private function validateStep(ScheduleCreateForm $model, int $step): bool
    {
        switch ($step) {
            case 1:
                return $model->validate(["date", "route_code"]);
            case 2:
                // После выбора направления устанавливаем route_id
                if (!empty($model->route_direction)) {
                    $model->setRouteId();
                }
                return $model->validate([
                    "date",
                    "route_code",
                    "route_direction",
                    "route_id",
                ]);
            case 3:
                return $model->validate([
                    "date",
                    "route_code",
                    "route_direction",
                    "route_id",
                    "car_id",
                ]);
            case 4:
                // Инициализируем данные остановок, если они еще не заполнены
                if (empty($model->stops_data)) {
                    $model->initializeStopsData();
                }
                return $model->validate();
            default:
                return false;
        }
    }

    /**
     * Finds the Schedule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Schedule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Schedule
    {
        if (($model = Schedule::findOne(["id" => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException("The requested page does not exist.");
    }

    /**
     * Updates a schedule group (all stops for a specific route/date/car).
     * @param string $date Date
     * @param int $car_id Car ID
     * @param int $route_id Route ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the schedule group cannot be found
     */
    public function actionUpdate(string $date, int $car_id, int $route_id): Response
    {
        // Получаем все записи расписания для этой группы
        $schedules = Schedule::find()
            ->where([
                "date" => $date,
                "car_id" => $car_id,
                "route_id" => $route_id,
            ])
            ->orderBy(["stop_number" => SORT_ASC])
            ->all();

        if (empty($schedules)) {
            throw new NotFoundHttpException("Расписание не найдено.");
        }

        if ($this->request->isPost) {
            $updated = 0;
            foreach ($schedules as $schedule) {
                $postData = $this->request->post();
                $scheduleKey = "Schedule_{$schedule->id}";

                if (isset($postData[$scheduleKey])) {
                    $allowedAttributes = [
                        "planned_time",
                        "actual_time",
                        "boarded_count",
                    ];

                    if (
                        $schedule->load([
                            $schedule->formName() => $postData[$scheduleKey],
                        ])
                    ) {
                        if (
                            $schedule->validate($allowedAttributes) &&
                            $schedule->save(false, $allowedAttributes)
                        ) {
                            $updated++;
                        }
                    }
                }
            }

            if ($updated > 0) {
                Yii::$app->session->setFlash(
                    "success",
                    "Обновлено {$updated} остановок.",
                );
                return $this->redirect([
                    "view",
                    "date" => $date,
                    "car_id" => $car_id,
                    "route_id" => $route_id,
                ]);
            }
        }

        return $this->render("update-group", [
            "schedules" => $schedules,
            "date" => $date,
            "car_id" => $car_id,
            "route_id" => $route_id,
        ]);
    }

    /**
     * Deletes a schedule group (all stops for a specific route/date/car).
     * @param string $date Date
     * @param int $car_id Car ID
     * @param int $route_id Route ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the schedule group cannot be found
     */
    public function actionDelete(string $date, int $car_id, int $route_id): Response {
        $deleted = Schedule::deleteAll([
            "date" => $date,
            "car_id" => $car_id,
            "route_id" => $route_id,
        ]);

        if ($deleted > 0) {
            Yii::$app->session->setFlash(
                "success",
                "Удалено {$deleted} записей расписания.",
            );
        } else {
            throw new NotFoundHttpException("Расписание не найдено.");
        }

        return $this->redirect(["index"]);
    }
}
