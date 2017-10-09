<?php

namespace frontend\controllers;

use Yii;
use frontend\models\LiniesComandes;
use frontend\models\Productes;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\components\Debug;

/**
 * LiniesComandesController implements the CRUD actions for LiniesComandes model.
 */
class LiniesComandesController extends Controller
{

    public function init()
    {
        $this->layout="/main.php";
        if(array_key_exists('isUser',$_SESSION)) { $this->layout="/main2.php"; }
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all LiniesComandes models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => LiniesComandes::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LiniesComandes model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new LiniesComandes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new LiniesComandes();
        $model->comanda_fk=$id;
        $model->quantitat_servida=0;

        if(Yii::$app->cache['llista_productes']==FALSE) { Productes::cacheProductes(); }
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(Yii::getalias('@web').'/index.php?r=comandes%2Fview&id='.$id);
        } else {
            return $this->render('create', [
                'model' => $model,
                'productes' => Yii::$app->cache['llista_productes'],
            ]);
        }
    }

    /**
     * Updates an existing LiniesComandes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if(Yii::$app->cache['linia'.$id]==FALSE) { LiniesComandes::cacheLinia($id); }
        $model = Yii::$app->cache['linia'.$id];

        if(Yii::$app->cache['llista_productes']==FALSE) { Productes::cacheProductes(); }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(Yii::getalias('@web').'/index.php?r=comandes%2Fview&id='.$model->comanda_fk);
        } else {
            return $this->render('update', [
                'model' => $model,
                'productes' => Yii::$app->cache['llista_productes'],
            ]);
        }
    }

    /**
     * Deletes an existing LiniesComandes model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model=$this->findModel($id);
        $comanda=$model->comanda_fk;
        $model->delete();
        LiniesComandes::cacheLinies($comanda);
        return $this->redirect(Yii::getalias('@web').'/index.php?r=comandes%2Fview&id='.$model->comanda_fk);
    }

    /**
     * Finds the LiniesComandes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LiniesComandes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LiniesComandes::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
