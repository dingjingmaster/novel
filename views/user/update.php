<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\NovelUser */

$this->title = 'Update Novel User: ' . $model->uid;
$this->params['breadcrumbs'][] = ['label' => 'Novel Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->uid, 'url' => ['view', 'id' => $model->uid]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="novel-user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
