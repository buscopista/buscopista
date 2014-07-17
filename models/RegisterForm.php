<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\Url;

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
    public function register($email)
    {
        // Inactive user until account is confirmed
        $model = new User();
        $model->status = 0;
        
        if ($this->validate() 
                && $model->load(Yii::$app->request->post(), 'RegisterForm')
                && $model->save()) {
            // Send confirmation e-mail
            return Yii::$app->mail->compose()
                ->setTo($model->mail)
                ->setFrom($email)
                ->setSubject(Yii::t('app', '[{sitename}] Confirm your account', [
                    'sitename' => Yii::$app->params['sitename']
                ]))
                ->setTextBody(Yii::t('app', 'You can confirm your account at: {link}', [
                    'link' => Url::to(['account/confirm', 'token' => $model->confirmToken])
                ]))
                ->send();
        }
        
        return false;
    }
}
