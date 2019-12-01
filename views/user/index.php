<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
?>

<?php if( !Yii::$app->session->get("user_id") ): ?>
    <?=  Html::a( 'Login', ['/user/login'] , ['class'=>'btn btn-primary']) ?>
    <?=  Html::a( 'Sign Up', ['/user/register'] , ['class'=>'btn btn-primary']) ?>
<?php else: ?>
<h1> Movies </h1>
<?=  Html::a( 'create new movie', ['/movie/create'] , ['class'=>'btn btn-primary']) ?>
<?php endif ?>

