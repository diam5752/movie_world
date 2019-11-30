<?php

namespace app\controllers;

use app\models\Movie;
use Yii;
use app\models\User;

use app\models\MovieSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class UserController extends \yii\web\Controller
{


    public function actionIndex( $user_id = null ,$order_by = 0)
    {

        $movie = new Movie();
        

        if( Yii::$app->session->get("user_id") ) {

            $user = User::findOne(['id' => Yii::$app->session->get("user_id")]);

            $user_movies = $movie->getMoviesAndLikes($user->id);
            
            return $this->render('/movie/index', [
                'movies' => $user_movies,
            ]);
        }
        
        $user_movies = $movie->getMovies( $user_id ,$order_by);

        return $this->render('/movie/index', [
            'movies' => $user_movies
        ]);

        
    }

    public function actionRegister()
    {
        $model = new \app\models\User();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                // form inputs are valid, do something here
                $model->save();
                Yii::$app->session->set("user_id", $model->id);

                return $this->redirect('index'); 
            }
        }

        return $this->render('register',['model'=> $model,]);
    }

    public function actionLogin(){
        $model = new \app\models\User();

        if (Yii::$app->request->post()) {

            $post = Yii::$app->request->post("User");

            $model = User::find()
                ->where([
                    "username"  => $post["username"],
                    "password" => $post["password"]
                ])
                ->one();
        
                if($model instanceof User){
                    Yii::$app->session->set("user_id", $model->id);
                    return $this->redirect('index'); 
                }

                else{
                    Yii::$app->session->setFlash('error', 'wrong credentials');
                    return $this->redirect('login'); 
                }
            
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }


    public function actionLogout(){
        Yii::$app->session->destroy();
        return $this->redirect('index'); 

    }


    public function actionUserMovies($user_id , $order_by = 0){
    

        $user_movies = new Movie();
        $user_movies = $user_movies->getMovies($user_id , $order_by);

        return $this->render('/movie/index', [
            'movies' => $user_movies
        ]);

    }

}
