<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Novel Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="novel-user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Novel User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'uid',
            'user_name',
            'password',
            'login',
            'email:email',
            //'sex',
            //'exp',
            //'recommend',
            //'register_time',
            //'last_login_ip',
            //'last_login_time',
            //'register',
            //'recommend_book:ntext',
            //'status',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
