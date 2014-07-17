<?php

namespace app\models;

use yii\helpers\Security;

class User extends MongoModel implements \yii\web\IdentityInterface
{
    const ROLE_ANONYMOUS = 0;
    const ROLE_ADMIN     = 1;
    const ROLE_SPORT     = 2;
    const ROLE_MANAGER   = 3;
        
    /**
     * @return array list of attribute names.
     */
    protected function _attributes()
    {
        return ['username', 'password', 'salt', 'mail', 'role', 'authKey', 
            'accessToken', 'confirmToken', 'resetPasswordToken'];
    }
    
    /**
     * @return array list of rules.
     */
    public function rules()
    {
        return [
            [['username', 'mail', 'password', 'role'], 'required'],
            [['username'], 'string', 'length' => [4, 32]],
            [['mail'], 'email'],
            [['username', 'mail'], 'unique', 'targetClass' => __CLASS__],
            [['password'], 'string', 'length' => [8, 128]],
            [['role'], 'in', 'range' => [
                self::ROLE_SPORT, 
                self::ROLE_MANAGER
            ]],
        ];
    }
    
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->salt = substr(Security::generateRandomKey(), -6);
                $this->password = Security::generatePasswordHash($this->password . $this->salt);
                $this->authKey = Security::generateRandomKey();
                $this->accessToken = Security::generateRandomKey();
                $this->confirmToken = Security::generateRandomKey();
            }
            return true;
        }
        return false;
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return self::_findOneBy(['_id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token)
    {
        return self::_findOneBy(['accessToken' => $token]);
    }
    
    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return self::_findOneBy(['username' => $username]);
    }
    
    /**
     * Finds user by mail
     *
     * @param  string      $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return self::_findOneBy(['mail' => $email]);
    }
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return (string)$this->_id;
    }
    
    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }
    
    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Security::validatePassword($password . $this->salt, $this->password);
    }
    
    /**
     * Generate reset password token
     * 
     * @return string reset password token
     */
    public function generateResetPasswordToken()
    {
        $this->resetPasswordToken = time() . '_' . Security::generateRandomKey();
        $this->update(false);
        return $this->resetPasswordToken;
    }
}