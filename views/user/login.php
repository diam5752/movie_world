<?php

use app\widgets\Alert;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form ActiveForm */
?>
<div class="user-register">

<?php if(Yii::$app->session->getFlash('error')): ?>
<?php endif; ?>

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'username')->textInput()->hint('Please enter your username') ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- user-register -->
