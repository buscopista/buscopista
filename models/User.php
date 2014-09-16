<?php

namespace app\models;

use yii\helpers\Security;

class User extends MongoModel implements \yii\web\IdentityInterface
{
    const SCENARIO_REGISTER         = 'create_account';
    const SCENARIO_UPDATE           = 'update_account';
    const SCENARIO_CHANGE_PASSWORD  = 'change_password';
    const SCENARIO_FORGOT_PASSWORD  = 'forgot_password';
    
    const ROLE_ANONYMOUS = 0;
    const ROLE_ADMIN     = 1;
    const ROLE_SPORT     = 2;
    const ROLE_MANAGER   = 3;

    /**
     * @return array a list of scenarios and the corresponding active attributes.
     */
    protected function _scenarios()
    {
        return [
            self::SCENARIO_REGISTER         => ['username', 'password', 'mail', 'role', 'status'],
            self::SCENARIO_UPDATE           => ['username', 'mail'],
            self::SCENARIO_CHANGE_PASSWORD  => ['password'],
            self::SCENARIO_FORGOT_PASSWORD  => ['resetPasswordToken'],
        ];
    }
    
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
        return array_merge(
            // Reused at account form
            self::rules_account(),
            // Reused at change password form
            self::rules_password(),
            [
                // Check register form
                [['role'], 'required'],
                [['role'], 'in', 'range' => [
                    self::ROLE_SPORT, 
                    self::ROLE_MANAGER
                ]],
            ]
        );
    }
    
    public function rules_account()
    {
        return [
            [['username', 'mail'], 'required'],
            [['username'], 'string', 'length' => [4, 32]],
            [['mail'], 'email'],
            [['username', 'mail'], 'unique', 'targetClass' => __CLASS__, 'filter' => function ($query) {
                // Fix unique fields update issue avoiding self instance check
                if (isset($this->_id)) {
                    $query->andWhere(
                        ['not in', '_id', [$this->_id]]
                    );
                }
            }],
        ];
    }
    
    public function rules_password()
    {
        return [
            [['password'], 'required'],
            [['password'], 'string', 'length' => [8, 128]],
        ];
    }
    
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                // Password security
                $this->salt = substr(Security::generateRandomKey(), -6);
                $this->generatePassword($this->password);
                // Identity security
                $this->authKey = Security::generateRandomKey();
                $this->accessToken = Security::generateRandomKey();
                $this->generateConfirmToken();
                // Node status (inactive by default)
                $this->status = 0;
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
     * Generate password
     * 
     * @param string $password
     */
    public function generatePassword($password) 
    {
        $this->password = Security::generatePasswordHash($password . $this->salt);
        return $this;
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
        return $this;
    }
    
    /**
     * Validates reset token and expiration
     *
     * @param  string  $token token to validate
     * @return boolean if token provided is valid
     */
    public function validateResetPasswordToken($token)
    {
        $time = (int) array_shift(explode('_', $token));
        return $time > strtotime('-1 week') && $token === $this->resetPasswordToken;
    }
    
    /**
     * Generate confirm token
     * 
     * @return string confirm token
     */
    public function generateConfirmToken()
    {
        $this->confirmToken = Security::generateRandomKey();
        return $this;
    }
    
    /**
     * Validates confirm token
     *
     * @param  string  $token token to validate
     * @return boolean if token provided is valid
     */
    public function validateConfirmToken($token)
    {
        return $token === $this->confirmToken;
    }
}