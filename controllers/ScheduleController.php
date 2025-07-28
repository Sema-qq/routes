<?php

namespace app\controllers;

use app\models\repository\Schedule;
use app\models\repository\ScheduleSearch;
use app\models\ScheduleCreateForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
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
     * Lists all Schedule models.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new ScheduleSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render("index", [
            "searchModel" => $searchModel,
            "dataProvider" => $dataProvider,
        ]);
    }

    /**
     * Displays a single Schedule model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id): string
    {
        return $this->render("view", [
            "model" => $this->findModel($id),
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
                        $schedule = $model->createSchedule();
                        if ($schedule) {
                            Yii::$app->session->setFlash(
                                "success",
                                "Расписание успешно создано.",
                            );
                            return $this->redirect([
                                "view",
                                "id" => $schedule->id,
                            ]);
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
                return $model->validate(["date", "car_id"]);
            case 2:
                return $model->validate(["date", "car_id", "route_id"]);
            case 3:
                // Валидируем основные поля и route_stop_key
                return $model->validate([
                    "date",
                    "car_id",
                    "route_id",
                    "route_stop_key",
                ]);
            case 4:
                return $model->validate();
            default:
                return false;
        }
    }

    /**
     * Updates an existing Schedule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);

        if (
            $this->request->isPost &&
            $model->load($this->request->post()) &&
            $model->save()
        ) {
            return $this->redirect(["view", "id" => $model->id]);
        }

        return $this->render("update", [
            "model" => $model,
        ]);
    }

    /**
     * Deletes an existing Schedule model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDelete(int $id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(["index"]);
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
}
