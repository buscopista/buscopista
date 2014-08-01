<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * PasswordForm is the model behind the change password form.
 */
class PasswordForm extends Model
{
    public $old_password;
    public $password;
    public $repeat_password;
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return array_merge(
            User::rules_password(),
            [
                [['old_password', 'password', 'repeat_password'], 'required'],
                ['old_password', 'validatePassword'],
                ['repeat_password', 'compare', 'compareAttribute' => 'password'],
            ]
        );
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->old_password)) {
                $this->addError('old_password', 'Incorrect password.');
            }
        }
    }
    
    /**
     * Edit user password
     * @return boolean whether the user is successfully stored
     */
    public function update()
    {
        $user = $this->getUser();
        $data = Yii::$app->request->post('PasswordForm');
        
        return 
            $this->validate() && 
            $user->generatePassword($data['password']) && 
            $user->save();
    }
    
    /**
     * Security reset
     */
    public function reset()
    {
        $this->old_password = '';
        $this->password = ''; 
        $this->repeat_password = ''; 
    }
    
    /**
     * Current user
     *
     * @return User|null
     */
    public function getUser()
    {
        return Yii::$app->user->getIdentity();
    }
}
