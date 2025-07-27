<?php

namespace app\controllers;

use app\models\repository\Schedule;
use app\models\repository\ScheduleSearch;
use app\models\ScheduleWizardForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

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
    public function actionView($id): string
    {
        return $this->render("view", [
            "model" => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Schedule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Schedule();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(["view", "id" => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render("create", [
            "model" => $model,
        ]);
    }

    /**
     * Мастер создания расписания - начальный экран с выбором даты
     * @return string|\yii\web\Response
     */
    public function actionWizard()
    {
        $model = new ScheduleWizardForm();
        $model->loadFromSession();

        if ($this->request->isPost) {
            $model->load($this->request->post());

            if (!empty($model->date)) {
                $model->current_step = 1;
                $model->saveToSession();
                return $this->redirect(["wizard-step", "step" => 1]);
            }
        } else {
            // Устанавливаем дату по умолчанию на сегодня
            $model->date = date("Y-m-d");
        }

        return $this->render("wizard/start", [
            "model" => $model,
        ]);
    }

    /**
     * Шаги мастера создания расписания
     * @param int $step Номер шага (1-4)
     * @return string|\yii\web\Response
     */
    public function actionWizardStep($step)
    {
        $step = (int) $step;
        if ($step < 1 || $step > 4) {
            return $this->redirect(["wizard"]);
        }

        $model = new ScheduleWizardForm();
        if (!$model->loadFromSession()) {
            return $this->redirect(["wizard"]);
        }

        // Проверяем, можно ли перейти к этому шагу
        if (!$model->canGoToStep($step)) {
            // Перенаправляем на первый доступный шаг
            for ($i = 1; $i <= 4; $i++) {
                if ($model->canGoToStep($i)) {
                    return $this->redirect(["wizard-step", "step" => $i]);
                }
            }
            return $this->redirect(["wizard"]);
        }

        $model->current_step = $step;

        if ($this->request->isPost) {
            $model->load($this->request->post());

            // Валидация в зависимости от шага
            $isValid = $this->validateStep($model, $step);

            if ($isValid) {
                $model->saveToSession();

                // Обработка кнопок навигации
                if (isset($_POST["next"])) {
                    if ($step < 4) {
                        $model->nextStep();
                        return $this->redirect([
                            "wizard-step",
                            "step" => $step + 1,
                        ]);
                    }
                } elseif (isset($_POST["previous"])) {
                    if ($step > 1) {
                        $model->previousStep();
                        return $this->redirect([
                            "wizard-step",
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

        return $this->render("wizard/step{$step}", [
            "model" => $model,
        ]);
    }

    /**
     * Возврат к определенному шагу мастера
     * @param int $step
     * @return \yii\web\Response
     */
    public function actionWizardGoToStep($step)
    {
        $step = (int) $step;
        if ($step < 1 || $step > 4) {
            return $this->redirect(["wizard"]);
        }

        $model = new ScheduleWizardForm();
        if (!$model->loadFromSession()) {
            return $this->redirect(["wizard"]);
        }

        if ($model->canGoToStep($step)) {
            $model->setStep($step);
            return $this->redirect(["wizard-step", "step" => $step]);
        }

        return $this->redirect(["wizard-step", "step" => $model->current_step]);
    }

    /**
     * Отмена мастера и очистка сессии
     * @return \yii\web\Response
     */
    public function actionWizardCancel()
    {
        $model = new ScheduleWizardForm();
        $model->clearSession();
        Yii::$app->session->setFlash("info", "Создание расписания отменено.");
        return $this->redirect(["index"]);
    }

    /**
     * Валидация конкретного шага
     * @param ScheduleWizardForm $model
     * @param int $step
     * @return bool
     */
    private function validateStep($model, $step)
    {
        switch ($step) {
            case 1:
                return $model->validate(["date", "car_id"]);
            case 2:
                return $model->validate(["date", "car_id", "route_id"]);
            case 3:
                return $model->validate([
                    "date",
                    "car_id",
                    "route_id",
                    "stop_id",
                    "stop_number",
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
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
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
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id): \yii\web\Response
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
    protected function findModel($id): Schedule
    {
        if (($model = Schedule::findOne(["id" => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException("The requested page does not exist.");
    }
}
