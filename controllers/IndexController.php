<?php
namespace app\controllers;
use yii\web\Controller;


class IndexController extends Controller
{
    public $layout = 'main';
    public function actionIndex()
    {
        return $this->render('index');
    }
}