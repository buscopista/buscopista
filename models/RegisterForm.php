<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class RegisterForm extends Model
{
    public $username;
    public $mail;
    public $password;
    public $role;
    public $verifyCode;
    
    public function rules() 
    {
        return array_merge(
            User::rules(), 
            [
                // verifyCode needs to be entered correctly
                ['verifyCode', 'captcha']
            ]
        );
    }
    
    /**
     * Register a new user
     * @return boolean whether the user is successfully stored
     */
    public function register()
    {
        $model = new User();
        
        if ($this->validate() 
                && $model->load(Yii::$app->request->post(), 'RegisterForm')
                && $model->save()) {
            // TODO Send confirmation mail instead of automatic login
            Yii::$app->session->setFlash('success', Yii::t('app', 'Welcome {username}!', array(
                'username' => $model->username
            )));
            return Yii::$app->user->login($model);
        }
        
        return false;
    }
}
