<?php

namespace frontend\controllers;

use Yii;
use common\models\Books;
use frontend\models\search\Books as BooksSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use common\models\Authors;

/**
 * BooksController implements the CRUD actions for Books model.
 */
class BooksController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Books models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BooksSearch();

        $session = Yii::$app->getSession();


        $params = Yii::$app->request->post();

        if ($params) {
            $session->set('books.filter', $params);
        }

        $params = $session->get('books.filter', []);

        $dataProvider = $searchModel->search($params);

        $query = Authors::find();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'modelsAuthors' => $query->all()
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model
        ]);
    }

    /**
     * Creates a new Books model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Books();

        $preview = UploadedFile::getInstance($model, 'preview');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if ($preview) {
                $pathInternal = Yii::$app->params['upload']['folders']['books'] . '/' . uniqid() . '.' . $preview->extension;
                $pathExternal = Yii::getAlias('@frontend') . '/web' . $pathInternal;

                if ($preview->saveAs($pathExternal)) {
                    $model->updateAttributes([
                        'preview' => $pathInternal
                    ]);
                }
            }

            return $this->redirect(['index']);
        } else {
            $query = Authors::find();

            return $this->render('create', [
                'model' => $model,
                'modelsAuthors' => $query->all()
            ]);
        }
    }

    /**
     * Updates an existing Books model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $preview = UploadedFile::getInstance($model, 'preview');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($preview) {
                $pathExternal = Yii::$app->params['upload']['folders']['books'] . '/' . uniqid() . '.' . $preview->extension;
                $pathInternal = Yii::getAlias('@frontend') . '/web' . $pathExternal;

                if ($preview->saveAs($pathInternal)) {
                    if ($model->preview) {
                        $pathOldInternal = Yii::getAlias('@frontend') . '/web' . $model->preview;

                        if (is_file($pathOldInternal)) {
                            unlink($pathOldInternal);
                        }
                    }

                    $model->updateAttributes([
                        'preview' => $pathExternal
                    ]);
                }
            }

            return $this->redirect(['index']);
        } else {

            $query = Authors::find();

            return $this->render('update', [
                'model' => $model,
                'modelsAuthors' => $query->all()
            ]);
        }
    }

    /**
     * Deletes an existing Books model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Books model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Books the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Books::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
