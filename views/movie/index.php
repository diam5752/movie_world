<?php

use app\models\Movie;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MovieSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Movie World';
?>


<div class="movie-index">



    <div class="container">

        <div class="row"> 
            <div class="col-sm-10"> 
                <h1><?= Html::encode($this->title) ?></h1>
            </div>
        
            <div class="col-sm-2"> 

                <?php if( Yii::$app->session->get("user_id")): ?>
                <p>
                    <?= Html::a('Create Movie', ['/movie/create'], ['class' => 'btn btn-success']) ?>
                </p>
                <?php else: ?>
                    <?=  Html::a( 'Login', ['/user/login'] , ['class'=>'btn btn-primary']) ?>
                    <?=  Html::a( 'Sign Up', ['/user/register'] , ['class'=>'btn btn-primary']) ?>
                <?php endif; ?>

            </div>

        </div>

        <div> Found <?= count($movies) ?>  movies </div>
    </div>

    <div class="container"> 

        <div class="col-sm-10"> 
            <?php foreach( $movies as $movie): ?>

                <div style="border: thin solid black" class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-2"> 
                            <p> 
                                <?= $movie["title"] ?>
                            </p>
                        </div>

                        <div class="col-sm-6" > </div>
                        <div class="col-sm-4"> 
                            <p> Posted in 
                                <?= $movie["date_published"] ?>
                            </p>
                        </div>

                    </div>
                    
                    <div class="row">
                        <div class="col-sm-12"> 
                            <p> 
                                <?= $movie["description"] ?>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-2"> 
                            <p> 
                            <?php if( isset($movie["like"]["0"]) && Yii::$app->session->get("user_id") ):?>
                                <span style="color:seagreen"> 
                                    <?= count($movie["likeCount"]) ?> 
                                    likes 
                                </span>
                            <?php else:?>
                                <span>
                                    <?= count($movie["likeCount"]) ?> 
                                    likes 
                                </span>
                            <?php endif;?>    
                            </p>
                        </div>

                        <div class="col-sm-2" > 
                            <p> 
                                <?= 88 ?> hates
                            </p>
                        </div>

                        <div class= "col-sm-4"> 
                        <?php if( Yii::$app->session->get("user_id")): ?>
                                <p>
                                    <?php 
                                        $url_like = Url::to(['/movie/like','user_id'=>Yii::$app->session->get("user_id"), 'movie_id' => $movie["id"] ]);
                                        $url_hate = Url::to(['/movie/hate','user_id'=>Yii::$app->session->get("user_id") ,'movie_id' => $movie["id"]]);

                                    ?>
                                    <?php if( isset($movie["like"]["0"])):?>
                                        <?= Html::a('Like', [$url_like], ['style' => "background-color:green; color:white" ]) ?>
                                    <?php else:?>
                                        <?= Html::a('Like', [$url_like] ) ?>
                                    <?php endif;?>    
                                    <span> | </span>
                                    <?= Html::a('Hate', [$url_hate]) ?>
                                </p>
                            <?php endif ?>
                        </div>

                        <div class="col-sm-4"> 
                            <p> Posted By 
                                <?php
                                $url = Url::to(['/user/user-movies','user_id'=>$movie["user"]["id"]]);
                                
                                echo Html::a( $movie["user"]["username"], [$url] );
                                ?>
                            </p>
                        </div>

                    </div>
                </div>

            <?php endforeach; ?>
       </div>

        <div class="col-sm-2"> 
            <p> Sort By: </p>
            <div class="col-sm-4"> 
                <p> 
                    <?php
                    $url = Url::to(['/user/user-movies','user_id'=>$movie["user"]["id"]]);
                                
                    echo Html::a( "Likes", [$url] );
                    ?>
                </p>
                <p> 
                    <?php
                    $url = Url::to(['/user/user-movies','user_id'=>$movie["user"]["id"]]);
                                
                    echo Html::a( "Hates" , [$url] );
                    ?>
                </p>
                <p> 
                    <?php
                    $url = Url::to(['/user/index', 'order_by' => Movie::ORDER_BY_DATE]);
                                
                    echo Html::a( "Date" , [$url] );
                    ?>
                </p>
            </div>
        </div>
    </div>


</div>
