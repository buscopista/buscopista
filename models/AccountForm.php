<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * AccountForm is the model behind the account form.
 */
class AccountForm extends Model
{
    public $username;
    public $mail;
    
    public function rules() 
    {
        return User::rules_account();
    }
    
    /**
     * Edit user account
     * @return boolean whether the user is successfully stored
     */
    public function update()
    {
        $user = Yii::$app->user->getIdentity();
        
        return 
            $this->validate() && 
            $user->load(Yii::$app->request->post(), 'AccountForm') && 
            // TODO Fix unique username or mail case !!!
            $user->save();
    }
}
