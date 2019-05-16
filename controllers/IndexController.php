<?php
namespace app\controllers;
use app\controllers\base\BaseController;

class IndexController extends BaseController
{
    public $layout = 'main';
    public function actionIndex()
    {
        $userLogin = $this->hasLogin();

        return $this->render('//site/index', [
            'userLogin' => $userLogin,
        ]);
    }
}