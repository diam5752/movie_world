<?php

namespace app\controllers;

use Yii;
use app\models\Movie;
use app\models\MovieLike;
use app\models\MovieSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MovieController implements the CRUD actions for Movie model.
 */
class MovieController extends Controller
{
    /**
     * {@inheritdoc}
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
     * Lists all Movie models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MovieSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Movie model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Movie model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Movie();
        
        if( !Yii::$app->session->get("user_id") ){
            return $this->redirect(['index']);
        }

        if( Yii::$app->request->post() ){
            $model = new Movie();
            $model->load(Yii::$app->request->post());
            $model->user_id = Yii::$app->session->get("user_id"); 
            $model->date_published = date("Y-m-d H:i:s");

            if($model->validate() && $model->save()){
                return $this->redirect(['view', 'id' => $model->id]);
            }
            print_r($model->errors); die();
        
        }
    

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Movie model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Movie model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Movie model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Movie the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Movie::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionLike( $user_id , $movie_id){

        $like_check = MovieLike::find()
            ->where(
                [
                    "user_id" => $user_id , 
                    "movie_id" => $movie_id
                    ]
                )
                ->all();
        
            
        if($like_check ){

            MovieLike::deleteAll([
                "user_id" => $user_id , 
                "movie_id" => $movie_id
            ]);
            return $this->redirect('/user/index');
        }
        
        $like = new MovieLike();
        $like->movie_id = $movie_id;
        $like->user_id = $user_id;
        $like->save();


        return $this->redirect('/user/index');
    }

    public function actionHate( $user_id , $movie_id){
        
        $like = new MovieLike();
        $like->movie_id = $movie_id;
        $like->user_id = $user_id;
        $like->save();


        return $this->redirect('/user/index');
    }

}
