<?php

namespace app\controllers;

use Yii;
use app\controllers\base\BaseController;

/**
 * UserController implements the CRUD actions for NovelUser model.
 */
class UserController extends BaseController
{
    /* 用户登录页 */
    public function actionLogin()
    {
        return $this->render('//site/register');

    }
}
