<?php

namespace app\controllers;

use app\models\repository\Route;
use app\models\repository\RouteSearch;
use app\models\repository\RouteStops;
use app\models\forms\CreateAutomaticRoute;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * RouteController implements the CRUD actions for Route model.
 */
class RouteController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            "verbs" => [
                "class" => VerbFilter::class,
                "actions" => [
                    "delete" => ["POST"],
                    "create-automatic" => ["POST"],
                ],
            ],
        ]);
    }

    /**
     * Lists all Route models.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new RouteSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Route model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Route model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Route();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->validate() && $model->save()) {
                // Сохраняем 10 остановок
                $stopIds = $model->stop_ids;
                foreach ($stopIds as $order => $stopId) {
                    if ($stopId) {
                        $routeStop = new RouteStops();
                        $routeStop->route_id = $model->id;
                        $routeStop->stop_id = $stopId;
                        $routeStop->stop_number = $order; // $order от 1 до 10
                        $routeStop->save();
                    }
                }
                Yii::$app->session->setFlash('success', 'Маршрут успешно создан!');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Route model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // При открытии формы заполни stop_ids существующими остановками
        if (empty($model->stop_ids)) {
            $model->stop_ids = \yii\helpers\ArrayHelper::map(
                $model->routeStops, // relation, должны быть в порядке stop_number
                'stop_number',
                'stop_id'
            );
            // если не гарантируется порядок в relation — сортируй:
            ksort($model->stop_ids);
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->validate() && $model->save()) {
            // Удалить старые остановки
            RouteStops::deleteAll(['route_id' => $model->id]);

            // Сохранить новые остановки
            $stopIds = $model->stop_ids;
            foreach ($stopIds as $order => $stopId) {
                if ($stopId) {
                    $routeStop = new RouteStops();
                    $routeStop->route_id = $model->id;
                    $routeStop->stop_id = $stopId;
                    $routeStop->stop_number = $order;
                    $routeStop->save();
                }
            }
            Yii::$app->session->setFlash('success', 'Маршрут обновлён!');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Route model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(["index"]);
    }

    /**
     * Создает автоматический маршрут (обратный или прямой) на основе существующего
     * @param int $id ID исходного маршрута
     * @return Response
     */
    public function actionCreateAutomatic(int $id): Response
    {
        $sourceRoute = Route::findOne($id);
        if (!$sourceRoute) {
            Yii::$app->session->setFlash("error", "Исходный маршрут не найден");
            return $this->redirect(["index"]);
        }

        $form = new CreateAutomaticRoute();
        $newRoute = $form->createRoute($sourceRoute);
        if ($newRoute == null) {
            // Получаем первую ошибку для отображения
            $error = $form->getFirstError(CreateAutomaticRoute::ERR_ATTR);
            $errorMessage = "Не удалось создать автоматический маршрут";

            if ($error) {
                $errorMessage = "{$errorMessage}: {$error}";
            }

            Yii::$app->session->setFlash("error", $errorMessage);
            return $this->redirect(["view", "id" => $id]);
        }

        Yii::$app->session->setFlash(
            "success",
            "Автоматический маршрут успешно создан",
        );
        return $this->redirect(["view", "id" => $newRoute->id]);
    }

    /**
     * Finds the Route model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Route the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): Route
    {
        if (($model = Route::findOne(["id" => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException("The requested page does not exist.");
    }
}
