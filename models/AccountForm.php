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
    
    public $_id; // Required by validator!
    
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
        $user->setScenario(User::SCENARIO_UPDATE);
        
        $this->_id = $user->_id; // Required by validator!
        
        return 
            $this->validate() && 
            $user->load(Yii::$app->request->post(), 'AccountForm') && 
            $user->save();
    }
}
