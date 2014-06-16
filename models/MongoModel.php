<?php

namespace app\models;

use yii\mongodb\ActiveRecord;
use yii\mongodb\Query;

abstract class MongoModel extends ActiveRecord
{    
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $now = time();
            if ($this->isNewRecord) {
                $this->created = $now;
            }
            $this->modified = $now;
            return true;
        }
        return false;
    }
    
    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return array_merge(
            ['_id', 'created', 'modified'], 
            $this->_attributes()
        );
    }
    
    /**
     * @return array list of attribute names.
     */
    abstract protected function _attributes();
    
    /**
     * Auxiliar method to find elements
     * 
     * @param array $params
     * @return \static
     */
    protected static function _findOneBy(array $params)
    {
        $query = new Query;
        $data = $query->from(self::collectionName())
                ->where($params)
                ->one();
        return new static($data);
    }
}