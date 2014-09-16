<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ResetPasswordForm is the model behind the reset password form.
 */
class ResetPasswordForm extends Model
{
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
                [['password', 'repeat_password'], 'required'],
                ['repeat_password', 'compare', 'compareAttribute' => 'password'],
            ]
        );
    }
    
    /**
     * Edit user password
     * @param User $user
     * @return boolean whether the user is successfully stored
     */
    public function update($user)
    {
        $user->setScenario(User::SCENARIO_CHANGE_PASSWORD);
        $data = Yii::$app->request->post('ResetPasswordForm');
        
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
        $this->password = ''; 
        $this->repeat_password = ''; 
    }
}
