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
    public function getHate()
    {
        return $this->hasMany(MovieHate::className(), ['movie_id' => 'id']);
    }
    
    public function getHateCount()
    {
        return $this->hasMany(MovieHate::className(), ['movie_id' => 'id']);
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
                    $order_by_likes = 1;
                    break;
                case Movie::ORDER_BY_HATES:
                    $order_by_hates = 1;
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
                },
                'hate',
                'hateCount' => function (ActiveQuery $q) {
                                $q->groupBy('movie_hate.movie_id');
                                $q->addSelect('movie_hate.id, movie_hate.movie_id ,count(movie_hate.id) as hate_count');
                                return   $q;
        },
            ]
        )
        // ->joinWith('like')
        ->where($where_user_id)
        ->orderBy($order_by_query)
        ->asArray()
        ->all();


        if( isset($order_by_likes) ){

            usort( $user_movie , function( $b, $a ) {
            
                if( !isset($a['likeCount']['0']['like_count'] ) ){
                    $a['likeCount']['0']['like_count'] = 0;
                }
    
                if( !isset($b['likeCount']['0']['like_count'] ) ){
                    $b['likeCount']['0']['like_count'] = 0;
                }
                
                return $a['likeCount']['0']['like_count'] <=> $b['likeCount']['0']['like_count'];
            } );

        }
    
        if( isset($order_by_hates) ){
        
            usort( $user_movie , function( $b, $a ) {
            
                if( !isset($a['hateCount']['0']['hate_count'] ) ){
                    $a['hateCount']['0']['hate_count'] = 0;
                }
            
                if( !isset($b['hateCount']['0']['hate_count'] ) ){
                    $b['hateCount']['0']['hate_count'] = 0;
                }
            
                return $a['hateCount']['0']['hate_count'] <=> $b['hateCount']['0']['hate_count'];
            } );
        }
        // echo "<pre>"; print_r($user_movie) ; die();

        return $user_movie;
    }

    public function getMoviesAndLikes($user_id , $order_by = 0){

        // $user_id = Yii::$app->session->get("user_id");
        $order_by_query = "";
        if( $order_by !== Movie::ORDER_BY_ID){

            switch ($order_by) {
                case Movie::ORDER_BY_DATE :
                    $order_by_query = "date_published DESC";
                    break;
                case Movie::ORDER_BY_LIKES:
                    $order_by_likes = 1;
                    break;
                case Movie::ORDER_BY_HATES:
                    $order_by_hates = 1;
                    break;
            }

        }

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
                },
                'hate' => function (ActiveQuery $q) use ($user_id) {
                            return   $q->andWhere(["movie_hate.user_id" => $user_id ]);
                            },
                'hateCount' => function (ActiveQuery $q) use ($user_id) {
                    $q->groupBy('movie_hate.movie_id');
                    $q->addSelect('movie_hate.id, movie_hate.movie_id ,count(movie_hate.id) as hate_count');
                    return   $q;
                },
            ]
        )
        ->orderBy($order_by_query)
        ->asArray()
        ->all();


        if( isset($order_by_likes) ){

            usort( $movie , function( $b, $a ) { 
            
                if( !isset($a['likeCount']['0']['like_count'] ) ){
                    $a['likeCount']['0']['like_count'] = 0;
                }
    
                if( !isset($b['likeCount']['0']['like_count'] ) ){
                    $b['likeCount']['0']['like_count'] = 0;
                }
                
                return $a['likeCount']['0']['like_count'] <=> $b['likeCount']['0']['like_count'];
            } );
        }
    
        if( isset($order_by_hates) ){
            
            usort( $movie , function( $b, $a ) {
            
                if( !isset($a['hateCount']['0']['hate_count'] ) ){
                    $a['hateCount']['0']['hate_count'] = 0;
                }
            
                if( !isset($b['hateCount']['0']['hate_count'] ) ){
                    $b['hateCount']['0']['hate_count'] = 0;
                }
            
                return $a['hateCount']['0']['hate_count'] <=> $b['hateCount']['0']['hate_count'];
            } );
        }
        


//         echo "<pre>"; print_r($movie) ; die();
        return $movie;
    }


}
