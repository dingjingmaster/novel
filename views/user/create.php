<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\NovelUser */

$this->title = 'Create Novel User';
$this->params['breadcrumbs'][] = ['label' => 'Novel Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="novel-user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
