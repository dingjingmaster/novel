<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\NovelUser */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="novel-user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'login')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sex')->textInput() ?>

    <?= $form->field($model, 'exp')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'recommend')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'register_time')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_login_ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_login_time')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'register')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'recommend_book')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
