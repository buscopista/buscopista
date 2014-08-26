<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\Url;

/**
 * ForgotForm is the model behind the forgot password form.
 */
class ForgotForm extends Model
{
    public $mail;
    
    private $_user = false;
    
    public function rules() 
    {
        return [
            ['mail', 'required'],
            ['mail', 'email'],
            ['mail', 'validateEmail']
        ];
    }
    
    /**
     * Validates the mail.
     * This method serves as the inline validation for password.
     */
    public function validateEmail()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            
            if (!$user) {
                $this->addError('mail', Yii::t('app', 'Email address not found'));
            }
        }
    }
    
    /**
     * Sends password to user's email
     * @return boolean whether the user exists and mail has been succesfully delivered
     */
    public function forgot()
    {
        if ($this->validate()) {
            
            $user = $this->getUser();
            $user->setScenario(User::SCENARIO_FORGOT_PASSWORD);
            
            // (Re)build token
            if ($user->generateResetPasswordToken() && $user->save()) {
                // Send recovery mail
                Yii::$app->mail->compose()
                    ->setTo($user->mail)
                    ->setFrom(Yii::$app->params['supportEmail'])
                    ->setSubject(Yii::t('app', '[{sitename}] Reset your password', [
                        'sitename' => Yii::$app->params['sitename']
                    ]))
                    ->setTextBody(Yii::t('app', 'You can reset your password at: {link}', [
                        'link' => Url::to(['account/reset', 'token' => $user->resetPasswordToken])
                    ]))
                    ->send();
                return true;
            }
        }
        
        $this->addError('mail', Yii::t('app', 'Unable to send email now, please try again later'));
        return false;
    }
    
    /**
     * Finds user by [[mail]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByEmail($this->mail);
        }
        
        return $this->_user;
    }
}
