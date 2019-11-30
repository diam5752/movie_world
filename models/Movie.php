<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "movie".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $user_id
 * @property string $date_published
 *
 * @property User $user
 */
class Movie extends \yii\db\ActiveRecord
{
    const ORDER_BY_ID = 0;
    const ORDER_BY_DATE = 1;
    const ORDER_BY_LIKES = 2;
    const ORDER_BY_HATES = 3;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movie';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'description', 'user_id'], 'required'],
            [['description'], 'string'],
            [['user_id'], 'integer'],
            [['date_published'], 'safe'],
            [['title'], 'string', 'max' => 50],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'user_id' => 'User ID',
            'date_published' => 'Date Published',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getLike()
    {
        return $this->hasMany(MovieLike::className(), ['movie_id' => 'id']);
    }

    public function getLikeCount()
    {
        return $this->hasMany(MovieLike::className(), ['movie_id' => 'id']);
    }



    public function getMoviesQuery(){
        $query = Movie::find()
        ->joinWith('user')
        ->joinWith('like');

        return $query;
    }

    public function getMovies( $user_id = null, $order_by = 0 ){

        $where_user_id = "";
        $order_by_query = "";
        
        if( isset($user_id) ){
            $where_user_id = ["user_id" => $user_id];
        }

        if( $order_by !== Movie::ORDER_BY_ID){

            switch ($order_by) {
                case Movie::ORDER_BY_DATE :
                    $order_by_query = "date_published DESC";
                    break;
                case Movie::ORDER_BY_LIKES:
                    $order_by_query = "likes ASC";
                    break;
                case Movie::ORDER_BY_HATES:
                    $order_by_query = "hates ASC";
                    break;
            }

        }
        
        $user_movie = Movie::find()
        ->with(
            [
                'like' ,
                'user',
                'likeCount' => function (ActiveQuery $q) {
                            $q->groupBy('movie_like.movie_id');
                            $q->addSelect('movie_like.id, movie_like.movie_id ,count(movie_like.id) as like_count');
                            return   $q;
                }
            ]
        )
        // ->joinWith('like')
        ->where($where_user_id)
        ->orderBy($order_by_query)
        ->asArray()
        ->all();

        return $user_movie;
    }

    public function getMoviesAndLikes($user_id){

        // $user_id = Yii::$app->session->get("user_id");
    
        $movie = Movie::find()
        ->with(
            [
                'like' => function (ActiveQuery $q) use ($user_id) {
                      return   $q->andWhere(["movie_like.user_id" => $user_id ]);
                }  ,
                'user',
                'likeCount' => function (ActiveQuery $q) use ($user_id) {
                            $q->groupBy('movie_like.movie_id');
                            $q->addSelect('movie_like.id, movie_like.movie_id ,count(movie_like.id) as like_count');
                            return   $q;
                }
            ]
        )
        ->asArray()
        ->all();


        usort( $movie , function( $a, $b ) { 
            
            if( !isset($a['likeCount']['0']['like_count'] ) ){
                $a['likeCount']['0']['like_count'] = 0;
            }

            if( !isset($b['likeCount']['0']['like_count'] ) ){
                $b['likeCount']['0']['like_count'] = 0;
            }
            
            return $a['likeCount']['0']['like_count'] <=> $b['likeCount']['0']['like_count'];
        } );

        echo "<pre>"; print_r($movie) ; die();
        return $movie;
    }


}
