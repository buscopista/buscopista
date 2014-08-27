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
    public $repeat_password;
    public $role;
    public $verifyCode;
    
    public function rules() 
    {
        return array_merge(
            User::rules(), 
            [
                ['repeat_password', 'required'],
                ['repeat_password', 'compare', 'compareAttribute' => 'password'],
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
        // Inactive user until account is confirmed
        $user = new User();
        $user->setScenario(User::SCENARIO_REGISTER);
        
        return 
            $this->validate() && 
            $user->load(Yii::$app->request->post(), 'RegisterForm') && 
            $user->save() &&
            $this->sendConfirmationMail($user);
    }
    
    public function sendConfirmationMail(User $user)
    {
        return Yii::$app->mail->compose()
            ->setTo($user->mail)
            ->setFrom(Yii::$app->params['supportEmail'])
            ->setSubject(Yii::t('app', '[{sitename}] Confirm your account', [
                'sitename' => Yii::$app->params['sitename']
            ]))
            ->setTextBody(Yii::t('app', 'You can confirm your account at: {link}', [
                'link' => Url::to([
                    'account/confirm', 
                    'username' => $user->username, 
                    'token'    => $user->confirmToken
                ])
            ]))
            ->send();
    }
    
    /**
     * Security reset
     */
    public function reset()
    {
        $this->password = ''; 
        $this->repeat_password = ''; 
    }
}
