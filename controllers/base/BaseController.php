<?php
namespace app\controllers\base;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\Session;

class BaseController extends Controller
{
    protected $cookie = null;
    protected $session = null;

    protected function hasLogin()
    {

    }

    protected function loginUserName()
    {

    }
}