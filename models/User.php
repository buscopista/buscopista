<?php

namespace app\models;

use yii\mongodb\ActiveRecord;
use yii\mongodb\Query;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'user';
    }
    
    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return ['_id', 'username', 'password', 'authKey', 'accessToken'];
    }
    
    protected static function _findOneBy($attr, $value)
    {
        $query = new Query;
        $data = $query->from('user')
                ->where([$attr => $value])
                ->one();
        return new self($data);
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return self::_findOneBy('_id', $id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token)
    {
        return self::_findOneBy('accessToken', $token);
    }
    
    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return self::_findOneBy('username', $username);
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
        return $this->password === $password;
    }
}
