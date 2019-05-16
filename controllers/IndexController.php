<?php
namespace app\controllers;
use app\controllers\base\BaseController;

class IndexController extends BaseController
{
    public $layout = 'main';
    public function actionIndex()
    {

        return $this->render('//site/index', [
            'a' => 'ddasdadadad',
        ]);
    }
}