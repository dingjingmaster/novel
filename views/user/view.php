<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\NovelUser */

$this->title = $model->uid;
$this->params['breadcrumbs'][] = ['label' => 'Novel Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="novel-user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->uid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->uid], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'uid',
            'user_name',
            'password',
            'login',
            'email:email',
            'sex',
            'exp',
            'recommend',
            'register_time',
            'last_login_ip',
            'last_login_time',
            'register',
            'recommend_book:ntext',
            'status',
        ],
    ]) ?>

</div>
