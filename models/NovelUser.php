<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "novel_user".
 *
 * @property string $uid
 * @property string $user_name 用户名
 * @property string $password 密码
 * @property string $login 登录次数
 * @property string $email 用户邮箱
 * @property int $sex 用户性别：1.男 0.女
 * @property string $exp 经验
 * @property string $recommend 推荐数
 * @property string $register_time 注册时间
 * @property string $last_login_ip 最后登录IP
 * @property string $last_login_time 最后登录时间
 * @property string $register 验证码
 * @property string $recommend_book 针对用户推荐的书籍
 * @property int $status 用户状态：1.可用
 */
class NovelUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'novel_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_name', 'password'], 'required'],
            [['login', 'sex', 'exp', 'recommend', 'register_time', 'last_login_time', 'status'], 'integer'],
            [['recommend_book'], 'string'],
            [['user_name', 'password', 'email', 'last_login_ip', 'register'], 'string', 'max' => 300],
            [['user_name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'uid' => 'Uid',
            'user_name' => 'User Name',
            'password' => 'Password',
            'login' => 'Login',
            'email' => 'Email',
            'sex' => 'Sex',
            'exp' => 'Exp',
            'recommend' => 'Recommend',
            'register_time' => 'Register Time',
            'last_login_ip' => 'Last Login Ip',
            'last_login_time' => 'Last Login Time',
            'register' => 'Register',
            'recommend_book' => 'Recommend Book',
            'status' => 'Status',
        ];
    }
}
